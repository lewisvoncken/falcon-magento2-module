<?php

namespace Hatimeria\Reagento\Plugin\Quote\Model;

use Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface;
use Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterfaceFactory;
use Hatimeria\Reagento\Api\Data\AdyenRedirectInterface;
use Hatimeria\Reagento\Api\Data\AdyenRedirectInterfaceFactory;
use Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceInterface;
use Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceInterfaceFactory;
use Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface;
use Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterfaceFactory;
use Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface;
use Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterfaceFactory;
use Hatimeria\Reagento\Api\Data\OrderResponseInterface;
use Hatimeria\Reagento\Api\Data\OrderResponseInterfaceFactory;
use Hatimeria\Reagento\Helper\Adyen;
use Magento\Framework\UrlInterface as MagentoUrlInterface;
use Magento\Quote\Model\QuoteManagement as MagentoQuoteManagement;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Api\OrderRepositoryInterface;

class QuoteManagement
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var MagentoUrlInterface
     */
    protected $urlBuilder;

    /** @var AdyenRedirectInterfaceFactory */
    protected $adyenRedirectFactory;

    /** @var OrderResponseInterfaceFactory */
    protected $orderResponseFactory;

    /** @var Adyen */
    protected $adyenHelper;

    /** @var AdyenRedirectAddressInterfaceFactory */
    protected $adyenRedirectAddressFactory;

    /** @var AdyenRedirectOpenInvoiceInterfaceFactory */
    protected $adyenRedirectOpenInvoiceFactory;

    /** @var AdyenRedirectOpenInvoiceItemInterfaceFactory */
    protected $adyenRedirectOpenInvoiceItemFactory;

    /** @var AdyenRedirectShopperInterfaceFactory */
    protected $adyenRedirectShopperFactory;

    /**
     * AfterPlaceOrder constructor.
     * @param OrderRepositoryInterface $orderRepository
     * @param MagentoUrlInterface $urlBuilder
     * @param AdyenRedirectInterfaceFactory $adyenRedirectFactory
     * @param AdyenRedirectAddressInterfaceFactory $adyenRedirectAddressFactory
     * @param AdyenRedirectOpenInvoiceInterfaceFactory $adyenRedirectOpenInvoiceFactory
     * @param AdyenRedirectOpenInvoiceItemInterfaceFactory $adyenRedirectOpenInvoiceItemFactory
     * @param AdyenRedirectShopperInterfaceFactory $adyenRedirectShopperFactory
     * @param OrderResponseInterfaceFactory $orderResponseFactory
     * @param Adyen $adyenHelper
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        MagentoUrlInterface $urlBuilder,
        AdyenRedirectInterfaceFactory $adyenRedirectFactory,
        AdyenRedirectAddressInterfaceFactory $adyenRedirectAddressFactory,
        AdyenRedirectOpenInvoiceInterfaceFactory $adyenRedirectOpenInvoiceFactory,
        AdyenRedirectOpenInvoiceItemInterfaceFactory $adyenRedirectOpenInvoiceItemFactory,
        AdyenRedirectShopperInterfaceFactory $adyenRedirectShopperFactory,
        OrderResponseInterfaceFactory $orderResponseFactory,
        Adyen $adyenHelper
    ) {
        $this->orderRepository = $orderRepository;
        $this->urlBuilder = $urlBuilder;
        $this->adyenRedirectFactory = $adyenRedirectFactory;
        $this->orderResponseFactory = $orderResponseFactory;
        $this->adyenHelper = $adyenHelper;
        $this->adyenRedirectAddressFactory = $adyenRedirectAddressFactory;
        $this->adyenRedirectOpenInvoiceFactory = $adyenRedirectOpenInvoiceFactory;
        $this->adyenRedirectOpenInvoiceItemFactory = $adyenRedirectOpenInvoiceItemFactory;
        $this->adyenRedirectShopperFactory = $adyenRedirectShopperFactory;
    }

    /**
     * @param MagentoQuoteManagement $subject
     * @param int $orderId
     * @return OrderResponseInterface | string
     */
    public function afterPlaceOrder(MagentoQuoteManagement $subject, $orderId)
    {
        /** @var Order $order */
        $order = $this->orderRepository->get($orderId);
        $payment = $order->getPayment();

        /** @var OrderResponseInterface $obj */
        $obj = $this->orderResponseFactory->create();
        $paymentAdditionalInfo = $payment->getAdditionalInformation();

        /** @var AdyenRedirectInterface $adyen */
        $adyen = $this->adyenRedirectFactory->create();
        if ($paymentAdditionalInfo) {
            if (
                $payment->getMethod() == 'adyen_cc'
                && isset($paymentAdditionalInfo['3dActive'])
                && true === $paymentAdditionalInfo['3dActive']
            ) {
                $this->getAdyenCcRedirectData($adyen, $paymentAdditionalInfo);
            } elseif ($payment->getMethod() == 'adyen_hpp') {
                $this->getAdyenHppRedirectData($adyen, $order, $payment);
            }
        }
        $obj->setAdyen($adyen);
        $obj->setOrderId($orderId);

        return $obj;
    }

    /**
     * @return string
     */
    protected function _getTermUrl()
    {
        return $this->urlBuilder->getUrl('adyen/process/validate3d');
    }

    /**
     * Set data for adyen cc redirecet
     *
     * @param AdyenRedirectInterface $adyen
     * @param $paymentAdditionalInfo
     */
    protected function getAdyenCcRedirectData(AdyenRedirectInterface $adyen, $paymentAdditionalInfo)
    {
        $adyen->setIssuerUrl($paymentAdditionalInfo['issuerUrl']);
        $adyen->setMd($paymentAdditionalInfo['md']);
        $adyen->setPaRequest($paymentAdditionalInfo['paRequest']);
        $adyen->setTermUrl($this->_getTermUrl());
    }

    /**
     * @param AdyenRedirectInterface $adyen
     * @param Order|OrderInterface $order
     * @param $payment
     */
    protected function getAdyenHppRedirectData(AdyenRedirectInterface $adyen, OrderInterface $order, $payment)
    {
        $fields = $this->adyenHelper->getRedirectHppFields($order, $payment);

        $adyen->setHppUrl($this->adyenHelper->getHppUrl($order, $payment));
        $adyen->setBillingAddress($this->getAddress($fields, 'billingAddress'));
        $adyen->setBlockedMethods($fields['blockedMethods']);
        $adyen->setBrandCode(isset($fields['brandCode']) ? $fields['brandCode'] : null);
        $adyen->setCountryCode(isset($fields['countryCode']) ? $fields['countryCode'] : null);
        $adyen->setCurrencyCode(isset($fields['currencyCode']) ? $fields['currencyCode'] : null);
        $adyen->setDeliveryAddress($this->getAddress($fields, 'deliveryAddress'));
        $adyen->setDfValue(isset($fields['dfValue']) ? $fields['dfValue'] : null);
        $adyen->setIssuerId(isset($fields['issuerId']) ? $fields['issuerId'] : null);
        $adyen->setMerchantAccount(isset($fields['merchantAccount']) ? $fields['merchantAccount'] : null);
        $adyen->setMerchantReference(isset($fields['merchantReference']) ? $fields['merchantReference'] : null);
        $adyen->setOpenInvoiceData($this->getOpenInvoice($fields));
        $adyen->setPaymentAmount(isset($fields['paymentAmount']) ? $fields['paymentAmount'] : null);
        $adyen->setResUrl(isset($fields['resURL']) ? $fields['resURL'] : null);
        $adyen->setSessionValidity(isset($fields['sessionValidity']) ? $fields['sessionValidity'] : null);
        $adyen->setShipBeforeDate(isset($fields['shipBeforeDate']) ? $fields['shipBeforeDate'] : null);
        $adyen->setShopper($this->getShopper($fields));
        $adyen->setShopperEmail(isset($fields['shopperEmail']) ? $fields['shopperEmail'] : null);
        $adyen->setShopperIp(isset($fields['shopperIP']) ? $fields['shopperIP'] : '');
        $adyen->setShopperLocale(isset($fields['shopperLocale']) ? $fields['shopperLocale'] : null);
        $adyen->setShopperReference(isset($fields['shopperReference']) ? $fields['shopperReference'] : null);
        $adyen->setSkinCode(isset($fields['skinCode']) ? $fields['skinCode'] : null);
        $adyen->setMerchantSig(isset($fields['merchantSig']) ? $fields['merchantSig'] : null);
    }

    /**
     * @param array $fields
     * @return AdyenRedirectShopperInterface
     */
    private function getShopper($fields)
    {
        /** @var AdyenRedirectShopperInterface $shopper */
        $shopper = $this->adyenRedirectShopperFactory->create();
        $shopper->setFirstName(isset($fields['shopper.firstName']) ? $fields['shopper.firstName'] : null);
        $shopper->setLastName(isset($fields['shopper.lastName']) ? $fields['shopper.lastName'] : null);
        $shopper->setGender(isset($fields['shopper.gender']) ? $fields['shopper.gender'] : null);
        $shopper->setTelephoneNumber(isset($fields['shopper.telephoneNumber']) ? $fields['shopper.telephoneNumber'] : null);
        $shopper->setDateOfBirthDayOfMonth(isset($fields['shopper.dateOfBirthDayOfMonth']) ? $fields['shopper.dateOfBirthDayOfMonth'] : null);
        $shopper->setDateOfBirthMonth(isset($fields['shopper.dateOfBirthMonth']) ? $fields['shopper.dateOfBirthMonth'] : null);
        $shopper->setDateOfBirthYear(isset($fields['shopper.dateOfBirthYear']) ? $fields['shopper.dateOfBirthYear'] : null);

        return $shopper;
    }

    /**
     * @param array $fields
     * @param string $prefix
     * @return AdyenRedirectAddressInterface
     */
    private function getAddress($fields, $prefix)
    {
        /** @var AdyenRedirectAddressInterface $billing */
        $address = $this->adyenRedirectAddressFactory->create();
        $address->setCity(isset($fields[$prefix . '.city']) ? $fields[$prefix . '.city'] : null);
        $address->setCountry(isset($fields[$prefix . '.country']) ? $fields[$prefix . '.country'] : null);
        $address->setStreet(isset($fields[$prefix . '.street']) ? $fields[$prefix . '.street'] : null);
        $address->setPostalCode(isset($fields[$prefix . '.postalCode']) ? $fields[$prefix . '.postalCode'] : null);
        $address->setHouseNumberOrName(isset($fields[$prefix . '.houseNumberOrName']) ? $fields[$prefix . '.houseNumberOrName'] : null);
        $address->setStateOrProvince(isset($fields[$prefix . '.stateOrProvince']) ? $fields[$prefix . '.stateOrProvince'] : null);

        return $address;
    }

    /**
     * @param array $fields
     * @return AdyenRedirectOpenInvoiceInterface
     */
    private function getOpenInvoice($fields)
    {
        /** @var AdyenRedirectOpenInvoiceInterface $openInvoice */
        $openInvoice = $this->adyenRedirectOpenInvoiceFactory->create();
        $items = [];
        for ($i = 1; $i <= $fields['openinvoicedata.numberOfLines']; $i++) {
            $itemPrefix = 'openinvoicedata.line' . $i . '.';
            /** @var AdyenRedirectOpenInvoiceItemInterface $openInvoiceItem */
            $openInvoiceItem = $this->adyenRedirectOpenInvoiceItemFactory->create();
            $openInvoiceItem->setCurrencyCode(isset($fields[$itemPrefix.'currencyCode']) ? $fields[$itemPrefix.'currencyCode'] : null);
            $openInvoiceItem->setDescription(isset($fields[$itemPrefix.'description']) ? $fields[$itemPrefix.'description'] : null);
            $openInvoiceItem->setItemAmount(isset($fields[$itemPrefix.'itemAmount']) ? $fields[$itemPrefix.'itemAmount'] : null);
            $openInvoiceItem->setItemVatAmount(isset($fields[$itemPrefix.'itemVatAmount']) ? $fields[$itemPrefix.'itemVatAmount'] : null);
            $openInvoiceItem->setItemVatPercentage(isset($fields[$itemPrefix.'itemVatPercentage']) ? $fields[$itemPrefix.'itemVatPercentage'] : null);
            $openInvoiceItem->setNumberOfItems(isset($fields[$itemPrefix.'numberOfItems']) ? $fields[$itemPrefix.'numberOfItems'] : null);
            $openInvoiceItem->setVatCategory(isset($fields[$itemPrefix.'vatCategory']) ? $fields[$itemPrefix.'vatCategory'] : null);
            $items[] = $openInvoiceItem;
        }
        $openInvoice->setItems($items);
        $openInvoice->setNumberOfLines(isset($fields['openinvoicedata.numberOfLines']) ? $fields['openinvoicedata.numberOfLines'] : null);
        $openInvoice->setRefundDescription(isset($fields['openinvoicedata.refundDescription']) ? $fields['openinvoicedata.refundDescription'] : null);

        return $openInvoice;
    }
}
