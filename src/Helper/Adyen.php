<?php

namespace Hatimeria\Reagento\Helper;

use Adyen\Payment\Helper\Data as AdyenHelper;
use Adyen\Payment\Observer\AdyenHppDataAssignObserver;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\UrlInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Tax\Model\Config as TaxConfig;
use Magento\Tax\Model\Calculation as TaxCalculation;

class Adyen extends AbstractHelper
{
    const ENDPOINT_PAY = 'pay';
    const ENDPOINT_SELECT = 'select';
    const ENDPOINT_SKIP_DETAILS = 'skipDetails';
    const ENDPOINT_DETAILS = 'details';

    const EMPTY_FIELD = 'NA';

    /** @var StoreManagerInterface */
    protected $storeManager;

    /** @var AdyenHelper */
    protected $adyenHelper;

    /** @var ResolverInterface */
    protected $resolver;

    /** @var TaxConfig */
    protected $taxConfig;

    /** @var TaxCalculation */
    protected $taxCalculation;

    /**
     * Adyen constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param AdyenHelper $adyenHelper
     * @param ResolverInterface $resolver
     * @param TaxConfig $taxConfig
     * @param TaxCalculation $taxCalculation
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        AdyenHelper $adyenHelper,
        ResolverInterface $resolver,
        TaxConfig $taxConfig,
        TaxCalculation $taxCalculation
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->adyenHelper = $adyenHelper;
        $this->resolver = $resolver;
        $this->taxConfig = $taxConfig;
        $this->taxCalculation = $taxCalculation;
    }

    /**
     * @param OrderInterface|Order $order
     * @param OrderPaymentInterface|Order\Payment $payment
     * @return string
     */
    public function getHppUrl(OrderInterface $order, OrderPaymentInterface $payment)
    {
        $url = "";
        if (!$payment) {
            return $url;
        }

        try {
            $paymentRoutine = $this->adyenHelper->getAdyenHppConfigData('payment_routine');
            $paymentMethodSelectionOnAdyen = $this->adyenHelper->getAdyenHppConfigDataFlag('payment_selection_on_adyen');
            $subdomain = $this->adyenHelper->isDemoMode() ? 'test' : 'live';
            $brandCode = $order->getPayment()->getAdditionalInformation('brand_code');
            $url = "https://{$subdomain}.adyen.com/hpp/";

            if ($paymentRoutine == 'single' && $paymentMethodSelectionOnAdyen) {
                $endpoint = self::ENDPOINT_PAY;
            } elseif ($paymentMethodSelectionOnAdyen) {
                $endpoint = self::ENDPOINT_SELECT;
            } elseif ($this->adyenHelper->isPaymentMethodOpenInvoiceMethod($brandCode)) {
                $endpoint = self::ENDPOINT_SKIP_DETAILS;
            } else {
                $endpoint = self::ENDPOINT_DETAILS;
            }
        } catch (\Exception $e) {
            $this->_logger->critical($e);
        }

        return $url . $endpoint . '.shtml';
    }

    /**
     * @param OrderInterface|Order $order
     * @param OrderPaymentInterface|Order\Payment $payment
     * @return array
     */
    public function getRedirectHppFields(OrderInterface $order, OrderPaymentInterface $payment)
    {
        $formFields = [];
        try {
            if ($payment) {

                $realOrderId = $order->getRealOrderId();
                $orderCurrencyCode = $order->getOrderCurrencyCode();
                $skinCode = trim($this->adyenHelper->getAdyenHppConfigData('skin_code'));
                $amount = $this->adyenHelper->formatAmount($order->getGrandTotal(), $orderCurrencyCode);
                $merchantAccount = trim($this->adyenHelper->getAdyenAbstractConfigData('merchant_account'));
                $shopperEmail = $order->getCustomerEmail();
                $customerId = $order->getCustomerId();
                $shopperIP = $order->getRemoteIp();
                $deliveryDays = $this->adyenHelper->getAdyenHppConfigData('delivery_days');
                $shopperLocale = trim($this->adyenHelper->getAdyenHppConfigData('shopper_locale')) ?: $this->resolver->getLocale();
                $countryCode = trim($this->adyenHelper->getAdyenHppConfigData('country_code'));
                $countryCode = (!empty($countryCode)) ? $countryCode : false;

                $recurringType = trim($this->adyenHelper->getAdyenAbstractConfigData('recurring_type'));
                $brandCode = $payment->getAdditionalInformation(AdyenHppDataAssignObserver::BRAND_CODE);
                $baseUrl = $this->storeManager->getStore($order->getStoreId())->getBaseUrl(UrlInterface::URL_TYPE_LINK);
                $issuerId = $payment->getAdditionalInformation('issuer_id');
                $dob = $order->getCustomerDob();

                // Paypal does not allow ONECLICK,RECURRING only RECURRING
                if ($brandCode == "paypal" && $recurringType == 'ONECLICK,RECURRING') {
                    $recurringType = "RECURRING";
                }

                // if directory lookup is enabled use the billingaddress as countrycode
                if ($countryCode == false) {
                    if ($order->getBillingAddress() &&
                        $order->getBillingAddress()->getCountryId() != "") {
                        $countryCode = $order->getBillingAddress()->getCountryId();
                    }
                }

                $formFields = [];

                $formFields['merchantAccount'] = $merchantAccount;
                $formFields['merchantReference'] = $realOrderId;
                $formFields['paymentAmount'] = (int)$amount;
                $formFields['currencyCode'] = $orderCurrencyCode;
                $formFields['shipBeforeDate'] = date(
                    "Y-m-d",
                    mktime(date("H"), date("i"), date("s"), date("m"), date("j") + $deliveryDays, date("Y"))
                );
                $formFields['skinCode'] = $skinCode;
                $formFields['shopperLocale'] = $shopperLocale;
                $formFields['countryCode'] = $countryCode;
                $formFields['shopperIP'] = $shopperIP;
                $formFields['sessionValidity'] = date(
                    DATE_ATOM,
                    mktime(date("H") + 1, date("i"), date("s"), date("m"), date("j"), date("Y"))
                );
                $formFields['shopperEmail'] = $shopperEmail;

                if ($customerId > 0) {
                    $formFields['recurringContract'] = $recurringType;
                    $formFields['shopperReference'] = $customerId;
                } else {
                    // required for openinvoice payment methods use unique id
                    $uniqueReference = "guest_" . $realOrderId . "_" . $order->getStoreId();
                    $formFields['shopperReference'] = $uniqueReference;
                }

                $formFields['blockedMethods'] = "";
                $formFields['resURL'] = $baseUrl . 'adyen/process/result';

                if ($brandCode) {
                    $formFields['brandCode'] = $brandCode;
                }

                if ($issuerId) {
                    $formFields['issuerId'] = $issuerId;
                }

                $formFields = $this->setBillingAddressData($order, $formFields);
                $formFields = $this->setShippingAddressData($order, $formFields);
                $formFields = $this->setOpenInvoiceData($order, $formFields);

                $formFields['shopper.gender'] = $this->getGenderText($order->getCustomerGender());

                if ($dob) {
                    $formFields['shopper.dateOfBirthDayOfMonth'] = trim($this->_getDate($dob, 'd'));
                    $formFields['shopper.dateOfBirthMonth'] = trim($this->_getDate($dob, 'm'));
                    $formFields['shopper.dateOfBirthYear'] = trim($this->_getDate($dob, 'Y'));
                }

                // For klarna acceptPrivacyPolicy to skip HPP page
                if ($brandCode == "klarna") {
                    //  // needed for DE and AT
                    $formFields['klarna.acceptPrivacyPolicy'] = 'true';
                }

                // OpenInvoice don't allow to edit billing and delivery items

                if ($this->adyenHelper->isPaymentMethodOpenInvoiceMethod($brandCode)) {
                    // don't allow editable shipping/delivery address
                    $formFields['billingAddressType'] = "1";
                    $formFields['deliveryAddressType'] = "1";

                    // make setting to make this optional
                    $formFields['shopperType'] = "1";
                }

                if ($payment->getAdditionalInformation("df_value") != "") {
                    $formFields['dfValue'] = $payment->getAdditionalInformation("df_value");
                }

                // Sort the array by key using SORT_STRING order
                ksort($formFields, SORT_STRING);

                // Generate the signing data string
                $signData = implode(":", array_map([$this, 'escapeString'],
                    array_merge(array_keys($formFields), array_values($formFields))));

                $hmacKey = $this->adyenHelper->getHmac();
                $merchantSig = base64_encode(hash_hmac('sha256', $signData, pack("H*", $hmacKey), true));

                $formFields['merchantSig'] = $merchantSig;
            }

        } catch (\Exception $e) {
            // do nothing for now
        }
        return $formFields;
    }

    /**
     * @param OrderInterface|Order $order
     * @param array $formFields
     * @return array
     */
    protected function setBillingAddressData(OrderInterface $order, $formFields)
    {
        $billingAddress = $order->getBillingAddress();

        if ($billingAddress) {

            $formFields['shopper.firstName'] = trim($billingAddress->getFirstname());
            $middleName = trim($billingAddress->getMiddlename());
            if ($middleName != "") {
                $formFields['shopper.infix'] = trim($middleName);
            }

            $formFields['shopper.lastName'] = trim($billingAddress->getLastname());
            $formFields['shopper.telephoneNumber'] = trim($billingAddress->getTelephone());

            $formFields = $this->setAddressData($formFields, $billingAddress, 'billingAddress');
        }
        return $formFields;
    }

    /**
     * Set Shipping Address data
     *
     * @param OrderInterface|Order $order
     * @param array $formFields
     * @return array
     */
    protected function setShippingAddressData(OrderInterface $order, $formFields)
    {
        $shippingAddress = $order->getShippingAddress();

        if ($shippingAddress) {
            $formFields = $this->setAddressData($formFields, $shippingAddress, 'deliveryAddress');
        }
        return $formFields;
    }

    /**
     * @param array $formFields
     * @param OrderAddressInterface $address
     * @param string $prefix
     * @return array
     */
    protected function setAddressData($formFields, OrderAddressInterface $address, $prefix)
    {
        $street = $this->adyenHelper->getStreet($address);

        if (isset($street['name']) && $street['name'] != "") {
            $formFields[$prefix . '.street'] = $street['name'];
        }

        if (isset($street['house_number']) && $street['house_number'] != "") {
            $formFields[$prefix . '.houseNumberOrName'] = $street['house_number'];
        } else {
            $formFields[$prefix . '.houseNumberOrName'] = "NA";
        }

        $formFields[$prefix . '.city'] = trim($address->getCity()) ?: self::EMPTY_FIELD;
        $formFields[$prefix . '.postalCode'] = trim($address->getPostcode()) ?: self::EMPTY_FIELD;
        $formFields[$prefix . '.stateOrProvince'] = trim($address->getRegionCode()) ?: self::EMPTY_FIELD;
        $formFields[$prefix . '.country'] = trim($address->getCountryId()) ?: self::EMPTY_FIELD;

        return $formFields;
    }

    /**
     * @param OrderInterface|Order $order
     * @param array $formFields
     * @return array
     */
    protected function setOpenInvoiceData(OrderInterface $order, $formFields)
    {
        $count = 0;
        $currency = $order->getOrderCurrencyCode();

        $formFields = $this->setOpenInvoiceItems($order, $formFields, $currency, $count);
        $formFields = $this->setOpenInvoiceDiscount($order, $formFields, $currency, $count);
        $formFields = $this->setOpenInvoiceShipping($order, $formFields, $currency, $count);

        $formFields['openinvoicedata.refundDescription'] = "Refund / Correction for " . $formFields['merchantReference'];
        $formFields['openinvoicedata.numberOfLines'] = $count;

        return $formFields;
    }

    /**
     * Set open invoice data with order items
     *
     * @param OrderInterface $order
     * @param array $formFields
     * @param string $currency
     * @param int $count
     * @return array
     */
    protected function setOpenInvoiceItems(OrderInterface $order, $formFields, $currency, &$count)
    {
        foreach ($order->getAllVisibleItems() as $item) {
            ++$count;
            $description = str_replace("\n", '', trim($item->getName()));
            $itemAmount = $this->adyenHelper->formatAmount($item->getPrice(), $currency);
            $itemVatAmount =
                ($item->getTaxAmount() > 0 && $item->getPriceInclTax() > 0) ?
                    $this->adyenHelper->formatAmount(
                        $item->getPriceInclTax(),
                        $currency
                    ) - $this->adyenHelper->formatAmount(
                        $item->getPrice(),
                        $currency
                    ) : $this->adyenHelper->formatAmount($item->getTaxAmount(), $currency);


            // Calculate vat percentage
            $itemVatPercentage = $this->adyenHelper->getMinorUnitTaxPercent($item->getTaxPercent());

            $numberOfItems = (int)$item->getQtyOrdered();

            $formFields = $this->setOpenInvoiceLineData(
                $order,
                $formFields,
                $count,
                $currency,
                $description,
                $itemAmount,
                $itemVatAmount,
                $itemVatPercentage,
                $numberOfItems
            );
        }

        return $formFields;
    }

    /**
     * Set open invoice data with discount
     *
     * @param OrderInterface $order
     * @param array $formFields
     * @param string $currency
     * @param int $count
     * @return array
     */
    public function setOpenInvoiceDiscount(OrderInterface $order, $formFields, $currency, &$count)
    {
        if ($order->getDiscountAmount() > 0 || $order->getDiscountAmount() < 0) {
            ++$count;
            $description = __('Total Discount');
            $itemAmount = $this->adyenHelper->formatAmount($order->getDiscountAmount(), $currency);
            $itemVatAmount = "0";
            $itemVatPercentage = "0";
            $numberOfItems = 1;

            $formFields = $this->setOpenInvoiceLineData(
                $order,
                $formFields,
                $count,
                $currency,
                $description,
                $itemAmount,
                $itemVatAmount,
                $itemVatPercentage,
                $numberOfItems
            );
        }

        return $formFields;
    }

    /**
     * Set open invoice data with shipping
     *
     * @param OrderInterface $order
     * @param array $formFields
     * @param string $currency
     * @param int $count
     * @return array
     */
    public function setOpenInvoiceShipping(OrderInterface $order, $formFields, $currency, &$count)
    {
        // Shipping cost
        if ($order->getShippingAmount() > 0 || $order->getShippingTaxAmount() > 0) {
            ++$count;
            $description = $order->getShippingDescription();
            $itemAmount = $this->adyenHelper->formatAmount($order->getShippingAmount(), $currency);
            $itemVatAmount = $this->adyenHelper->formatAmount($order->getShippingTaxAmount(), $currency);

            // Create RateRequest to calculate the Tax class rate for the shipping method
            $rateRequest = $this->taxCalculation->getRateRequest(
                $order->getShippingAddress(),
                $order->getBillingAddress(),
                null,
                $order->getStoreId(), $order->getCustomerId()
            );

            $taxClassId = $this->taxConfig->getShippingTaxClass($order->getStoreId());
            $rateRequest->setProductClassId($taxClassId);
            $rate = $this->taxCalculation->getRate($rateRequest);

            $itemVatPercentage = $this->adyenHelper->getMinorUnitTaxPercent($rate);
            $numberOfItems = 1;

            $formFields = $this->setOpenInvoiceLineData($order, $formFields, $count, $currency, $description, $itemAmount,
                $itemVatAmount, $itemVatPercentage, $numberOfItems);
        }

        return $formFields;
    }


    /**
     * Set the openinvoice line
     *
     * @param OrderInterface|Order $order
     * @param array $formFields
     * @param int $count
     * @param string $currencyCode
     * @param string $description
     * @param int $itemAmount
     * @param int $itemVatAmount
     * @param float $itemVatPercentage
     * @param int $numberOfItems
     * @return array
     */
    protected function setOpenInvoiceLineData(
        OrderInterface $order,
        $formFields,
        $count,
        $currencyCode,
        $description,
        $itemAmount,
        $itemVatAmount,
        $itemVatPercentage,
        $numberOfItems
    ) {
        $linename = "line" . $count;
        $formFields['openinvoicedata.' . $linename . '.currencyCode'] = $currencyCode;
        $formFields['openinvoicedata.' . $linename . '.description'] = $description;
        $formFields['openinvoicedata.' . $linename . '.itemAmount'] = $itemAmount;
        $formFields['openinvoicedata.' . $linename . '.itemVatAmount'] = $itemVatAmount;
        $formFields['openinvoicedata.' . $linename . '.itemVatPercentage'] = $itemVatPercentage;
        $formFields['openinvoicedata.' . $linename . '.numberOfItems'] = $numberOfItems;

        if ($this->adyenHelper->isVatCategoryHigh($order->getPayment()->getAdditionalInformation(AdyenHppDataAssignObserver::BRAND_CODE))) {
            $formFields['openinvoicedata.' . $linename . '.vatCategory'] = "High";
        } else {
            $formFields['openinvoicedata.' . $linename . '.vatCategory'] = "None";
        }
        return $formFields;
    }

    /**
     * @param null $date
     * @param string $format
     * @return mixed
     */
    protected function _getDate($date = null, $format = 'Y-m-d H:i:s')
    {
        if (strlen($date) < 0) {
            $date = date('d-m-Y H:i:s');
        }
        $timeStamp = new \DateTime($date);
        return $timeStamp->format($format);
    }


    /**
     * @param $genderId
     * @return string
     */
    protected function getGenderText($genderId)
    {
        $result = "";
        if ($genderId == '1') {
            $result = 'MALE';
        } elseif ($genderId == '2') {
            $result = 'FEMALE';
        }
        return $result;
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
}