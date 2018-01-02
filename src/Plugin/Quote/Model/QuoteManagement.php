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
    /** @var OrderRepositoryInterface */
    protected $orderRepository;

    /** @var MagentoUrlInterface */
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
        $adyen->setBillingAddress($this->getAddress($fields, 'billingAddress.'));
        $adyen->setDeliveryAddress($this->getAddress($fields, 'deliveryAddress.'));
        $adyen->setOpenInvoiceData($this->getOpenInvoice($fields));
        $adyen->setShopper($this->getShopper($fields));
        $adyen->setBlockedMethods($fields['blockedMethods']);
        $adyen->setResUrl(isset($fields['resURL']) ? $fields['resURL'] : null);
        $adyen->setShopperIp(isset($fields['shopperIP']) ? $fields['shopperIP'] : '');

        $fieldsToCopy = [
            'brandCode',
            'countryCode',
            'currencyCode',
            'dfValue',
            'issuerId',
            'merchantAccount',
            'merchantReference',
            'paymentAmount',
            'sessionValidity',
            'shipBeforeDate',
            'shopperEmail',
            'shopperLocale',
            'shopperReference',
            'skinCode',
            'merchantSig'
        ];
        $this->populateObjectFromArray($adyen, $fields, $fieldsToCopy);
    }

    /**
     * @param array $fields
     * @return AdyenRedirectShopperInterface
     */
    private function getShopper($fields)
    {
        /** @var AdyenRedirectShopperInterface $shopper */
        $shopper = $this->adyenRedirectShopperFactory->create();
        $fieldsToCopy = [
            'firstName',
            'lastName',
            'gender',
            'telephoneNumber',
            'dateOfBirthDayOfMonth',
            'dateOfBirthMonth',
            'dateOfBirthYear'
        ];
        $this->populateObjectFromArray($shopper, $fields, $fieldsToCopy, 'shopper.');

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
        $fieldsToCopy = [
            'city',
            'country',
            'street',
            'postalCode',
            'houseNumberOrName',
            'stateOrProvince'
        ];
        $this->populateObjectFromArray($address, $fields, $fieldsToCopy, $prefix);
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

            $fieldsToCopy = [
                'currencyCode',
                'description',
                'itemAmount',
                'itemVatAmount',
                'itemVatPercentage',
                'numberOfItems',
                'vatCategory'
            ];
            $this->populateObjectFromArray($openInvoiceItem, $fields, $fieldsToCopy, $itemPrefix);
            $items[] = $openInvoiceItem;
        }
        $openInvoice->setItems($items);
        $openInvoice->setNumberOfLines(isset($fields['openinvoicedata.numberOfLines']) ? $fields['openinvoicedata.numberOfLines'] : null);
        $openInvoice->setRefundDescription(isset($fields['openinvoicedata.refundDescription']) ? $fields['openinvoicedata.refundDescription'] : null);

        return $openInvoice;
    }

    /**
     * @param $object
     * @param array $data
     * @param array $keys
     * @param string $prefix
     */
    protected function populateObjectFromArray($object, array $data, array $keys = null, $prefix = '') {
        $keys = $keys ?: array_keys($data);

        foreach ($keys as $key) {
            $method = 'set' . ucwords($key);
            $value = isset($data[$prefix . $key]) ? $data[$prefix . $key] : null;
            $object->$method($value);
        }
    }
}
