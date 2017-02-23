<?php
namespace Hatimeria\Reagento\Model\Payment;

use Hatimeria\Reagento\Api\Payment\AdyenLinkInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Adyen\Payment\Helper\Data as AdyenHelper;
use Adyen\Payment\Gateway\Command\PayByMailCommand;

/**
 * Class Payment
 * @package Hatimeria\Reagento\Model\Payment
 */
class AdyenHPP implements AdyenLinkInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var AdyenHelper
     */
    protected $adyenHelper;

    /**
     * @var ResolverInterface
     */
    protected $resolver;

    /**
     * @var StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        AdyenHelper $adyenHelper,
        ResolverInterface $resolver,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        EncryptorInterface $encryptor,
        QuoteIdMaskFactory $quoteIdMaskFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->adyenHelper = $adyenHelper;
        $this->resolver = $resolver;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->encryptor = $encryptor;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
    }

    public function getGuestOrderPaymentLink($orderId, $cartId)
    {
       /** @var OrderInterface $order */
       $order = $this->orderRepository->get($orderId);

       $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

       if ($quoteIdMask->getQuoteId() !== $order->getQuoteId()) {
           //incorrect entry data
           return false;
       }

       if ($order->getPayment()->getMethod() == 'adyen_hpp') {
           return $this->getPaylinkForOrder($order);
       }

       return false;
   }

    public function getCustomerOrderPaymentLink($orderId, $customerId)
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->get($orderId);

        if ($order->getCustomerId() !== $customerId) {
            //incorrect entry data
            return false;
        }

        if ($order->getPayment()->getMethod() == 'adyen_hpp') {
            return $this->getPaylinkForOrder($order);
        }

        return false;
    }

    /**
     * Generate payment link for Adyen
     *
     * @param OrderInterface $order
     * @return string
     */
    public function getPaylinkForOrder(OrderInterface $order)
    {
        $url = $this->getFormUrl();
        $fields = $this->getFormFields($order->getPayment());

        $count = 1;
        $size = count($fields);
        foreach ($fields as $field => $value) {
            if ($count == 1) {
                $url .= "?";
            }
            $url .= urlencode($field) . "=" . urlencode($value);

            if ($count != $size) {
                $url .= "&";
            }

            ++$count;
        }
        return $url;
    }

    /**
     * @return string
     */
    public function getFormUrl()
    {
        if ($this->adyenHelper->isDemoMode()) {
            $url = 'https://test.adyen.com/hpp/pay.shtml';
        } else {
            $url = 'https://live.adyen.com/hpp/pay.shtml';
        }
        return $url;
    }

    /**
     * @param $payment
     * @return array
     */
    protected function getFormFields($payment)
    {
        /** @var OrderInterface $order */
        $order = $payment->getOrder();

        $realOrderId       = $order->getRealOrderId();
        $orderCurrencyCode = $order->getOrderCurrencyCode();

        // check if paybymail has it's own skin
        $skinCode          = trim($this->adyenHelper->getAdyenPayByMailConfigData('skin_code'));
        if ($skinCode == "") {
            // use HPP skin and HMAC
            $skinCode = $this->adyenHelper->getAdyenHppConfigData('skin_code', $order->getStoreId());
            $hmacKey           = $this->getHmac($order->getStoreId());
        } else {
            // use pay_by_mail skin and hmac
            $hmacKey = $this->adyenHelper->getHmacPayByMail();
        }

        $amount            = $this->adyenHelper->formatAmount($order->getGrandTotal(), $orderCurrencyCode);
        $merchantAccount   = trim($this->adyenHelper->getAdyenAbstractConfigData('merchant_account'));
        $shopperEmail      = $order->getCustomerEmail();
        $customerId        = $order->getCustomerId();
        $shopperLocale     = trim($this->adyenHelper->getAdyenHppConfigData('shopper_locale'));
        $shopperLocale     = (!empty($shopperLocale)) ? $shopperLocale : $this->resolver->getLocale();
        $countryCode       = trim($this->adyenHelper->getAdyenHppConfigData('country_code'));
        $countryCode       = (!empty($countryCode)) ? $countryCode : false;

        // if directory lookup is enabled use the billingadress as countrycode
        if ($countryCode == false) {
            if (is_object($order->getBillingAddress()) && $order->getBillingAddress()->getCountry() != "") {
                $countryCode = $order->getBillingAddress()->getCountry();
            } else {
                $countryCode = "";
            }
        }

        $deliveryDays                   = $this->adyenHelper->getAdyenHppConfigData('delivery_days');
        $deliveryDays                   = (!empty($deliveryDays)) ? $deliveryDays : 5;

        $formFields = [];
        $formFields['merchantAccount']   = $merchantAccount;
        $formFields['merchantReference'] = $realOrderId;
        $formFields['paymentAmount']     = (int)$amount;
        $formFields['currencyCode']      = $orderCurrencyCode;
        $formFields['shipBeforeDate']    = date(
            "Y-m-d",
            mktime(date("H"), date("i"), date("s"), date("m"), date("j") + $deliveryDays, date("Y"))
        );
        $formFields['skinCode']          = $skinCode;
        $formFields['shopperLocale']     = $shopperLocale;
        if ($countryCode != "") {
            $formFields['countryCode']       = $countryCode;
        }

        $formFields['shopperEmail']      = $shopperEmail;
        // recurring
        $recurringType                   = trim($this->adyenHelper->getAdyenAbstractConfigData('recurring_type'));

        $formFields['recurringContract'] = $recurringType;


        $sessionValidity = $this->adyenHelper->getAdyenPayByMailConfigData('session_validity');

        if ($sessionValidity == "") {
            $sessionValidity = 3;
        }

        $formFields['sessionValidity'] = date("c", strtotime("+". $sessionValidity. " days"));
        $formFields['shopperReference']  = (!empty($customerId)) ? $customerId : PayByMailCommand::GUEST_ID . $realOrderId;

        // Sort the array by key using SORT_STRING order
        ksort($formFields, SORT_STRING);

        // Generate the signing data string
        $signData = implode(":", array_map([$this, 'escapeString'],
            array_merge(array_keys($formFields), array_values($formFields))));

        $merchantSig = base64_encode(hash_hmac('sha256', $signData, pack("H*", $hmacKey), true));

        $formFields['merchantSig']      = $merchantSig;


        return $formFields;
    }



    /**
     * The character escape function is called from the array_map function in _signRequestParams
     *
     * @param $val
     * @return mixed
     */
    protected function escapeString($val)
    {
        return str_replace(':', '\\:', str_replace('\\', '\\\\', $val));
    }

    /**
     * Get HMAC keys for the skin
     *
     * @param int $storeId
     * @return string
     */
    public function getHmac($storeId)
    {
        switch ($this->adyenHelper->isDemoMode()) {
            case true:
                $secretWord =  trim($this->adyenHelper->getAdyenHppConfigData('hmac_test', $storeId));
                break;
            default:
                $secretWord = trim($this->adyenHelper->getAdyenHppConfigData('hmac_live', $storeId));
                break;
        }
        $decrypted = $this->encryptor->decrypt($secretWord);
        //if crypt key is empty then hmac string is saved in plain form and decryption returns empty string
        return $decrypted ? $decrypted : $secretWord;
    }
}
