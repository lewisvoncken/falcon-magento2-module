<?php
namespace Hatimeria\Reagento\Model\Payment;

use Hatimeria\Reagento\Api\Payment\PaypalInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\UrlInterface;
use Hatimeria\Reagento\Model\Payment\PaypalDataFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Paypal\Model\Express\Checkout\Factory as PaypalCheckoutFactory;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Checkout\Helper\Data;
use Magento\Checkout\Model\Type\Onepage;
use Magento\Paypal\Model\Express\Checkout;

/**
 * Class Payment
 * @package Hatimeria\Reagento\Model\Payment
 */
class Paypal implements PaypalInterface
{
    /**
     * Config mode type
     *
     * @var string
     */
    protected $_configType = 'Magento\Paypal\Model\Config';

    /**
     * Config method type
     *
     * @var string
     */
    protected $_configMethod = \Magento\Paypal\Model\Config::METHOD_WPP_EXPRESS;

    /**
     * Checkout mode type
     *
     * @var string
     */
    protected $_checkoutType = 'Magento\Paypal\Model\Express\Checkout';

    /**
     * @var \Magento\Paypal\Model\Config
     */
    protected $_config;

    /**
     * Url
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Payment Data Factory
     * @var PaymentDataFactory
     */
    protected $factory;

    /**
     * Object Manager
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Quote Mask Factory
     * @var QuoteIdMaskFactory
     */
    protected $quoteMaskFactory;

    /**
     * Cart Repository
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * Quote
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /**
     * Paypal Checkout
     * @var \Magento\Paypal\Model\Express\Checkout
     */
    protected $_checkout;

    /**
     * Internal cache of checkout models
     *
     * @var array
     */
    protected $_checkoutTypes = [];

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var Checkout\Factory
     */
    protected $checkoutFactory;

    /**
     * Payment constructor.
     * @param UrlInterface $urlBuilder
     * @param PaypalDataFactory $factory
     * @param ObjectManagerInterface $objectManager
     * @param QuoteIdMaskFactory $quoteMaskFactory
     * @param CartRepositoryInterface $cartRepository
     * @param CustomerSession $customerSession
     * @param Checkout\Factory $checkoutFactory
     */
    public function __construct(
        UrlInterface $urlBuilder,
        PaypalDataFactory $factory,
        ObjectManagerInterface $objectManager,
        QuoteIdMaskFactory $quoteMaskFactory,
        CartRepositoryInterface $cartRepository,
        CustomerSession $customerSession,
        PaypalCheckoutFactory $checkoutFactory
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->factory = $factory;
        $this->objectManager = $objectManager;
        $this->quoteMaskFactory = $quoteMaskFactory;
        $this->cartRepository = $cartRepository;
        $this->customerSession = $customerSession;
        $this->checkoutFactory = $checkoutFactory;
        $parameters = ['params' => [$this->_configMethod]];
        $this->_config = $this->objectManager->create($this->_configType, $parameters);
    }

    /**
     * Quote
     * @return \Magento\Quote\Model\Quote
     */
    protected function _getQuote()
    {
        return $this->quote;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return GetToken
     */
    public function setQuote(\Magento\Quote\Model\Quote $quote)
    {
        $this->quote = $quote;

        return $this;
    }

    /**
     * Instantiate quote and checkout
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _initCheckout()
    {
        $quote = $this->_getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t initialize Express Checkout.'));
        }
        if (!isset($this->_checkoutTypes[$this->_checkoutType])) {
            $parameters = [
                'params' => [
                    'quote' => $quote,
                    'config' => $this->_config,
                ],
            ];
            $this->_checkoutTypes[$this->_checkoutType] = $this->checkoutFactory
                ->create($this->_checkoutType, $parameters);
        }
        $this->_checkout = $this->_checkoutTypes[$this->_checkoutType];
    }

    /**
     * @return string|null
     * @param $cartId string
     * @throws LocalizedException
     */
    protected function createToken($cartId)
    {
        $this->_initCheckout();
        $quote = $this->_getQuote();
        $hasButton = false; // @todo needs to be parametrized. Parameter: button=[1 / 0]

        /** @var Data $checkoutHelper */
        $checkoutHelper = $this->objectManager->get(Data::class);
        $quoteCheckoutMethod = $quote->getCheckoutMethod();
        $customerData = $this->customerSession->getCustomerDataObject();

        if ($quote->getIsMultiShipping()) {
            $quote->setIsMultiShipping(false);
            $quote->removeAllAddresses();
        }

        if ($customerData->getId()) {
            $this->_checkout->setCustomerWithAddressChange(
                $customerData,
                $quote->getBillingAddress(),
                $quote->getShippingAddress()
            );
        } else if (
            (!$quoteCheckoutMethod || $quoteCheckoutMethod !== Onepage::METHOD_REGISTER)
            && !$checkoutHelper->isAllowedGuestCheckout($quote, $quote->getStoreId())
        ) {
            throw new LocalizedException(__('To check out, please sign in with your email address.'));
        }

        // billing agreement
        $this->_checkout->setIsBillingAgreementRequested(false);

        // Bill Me Later
        $this->_checkout->setIsBml(false); // @todo needs to be parametrized. Parameter: bml=[1 / 0]

        // giropay
        $this->_checkout->prepareGiropayUrls(
            $this->urlBuilder->getUrl('checkout/onepage/success'),
            $this->urlBuilder->getUrl('paypal/express/cancel', ['cart_id' => $cartId]),
            $this->urlBuilder->getUrl('checkout/onepage/success')
        );

        return $this->_checkout->start(
            $this->urlBuilder->getUrl('checkoutExt/payment_paypal_express/return', ['cart_id' => $cartId]),
            $this->urlBuilder->getUrl('checkoutExt/payment_paypal_express', ['cart_id' => $cartId]),
            $hasButton
        );
    }

    /**
     * Initialize Quote based on masked Id
     * @param $cartId
     * @return \Magento\Quote\Model\Quote
     */
    protected function initQuote($cartId)
    {
        // Unmask quote:
        $quoteMask = $this->quoteMaskFactory->create()->load($cartId, 'masked_id');
        $this->setQuote($this->cartRepository->getActive($quoteMask->getQuoteId()));

        return $this->_getQuote();
    }


    /**
     * Fetch PayPal token
     * @param string $cartId
     * @return \Hatimeria\Reagento\Api\Payment\Data\PaypalDataInterface
     */
    public function getToken($cartId)
    {
        $this->initQuote($cartId);
        $paymentData = $this->factory->create();

        try {
            $token = $this->createToken($cartId);
            $url = $this->_checkout->getRedirectUrl();

            $paymentData->setToken($token);
            $paymentData->setUrl($url);
        } catch(LocalizedException $e) {
            $paymentData->setError($e->getMessage());
        }

        return $paymentData;
    }
}
