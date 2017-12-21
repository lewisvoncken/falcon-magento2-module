<?php

namespace Hatimeria\Reagento\Api\Data;

interface AdyenRedirectInterface
{
    const ISSUER_URL = 'issuer_url';
    const MD = 'md';
    const PA_REQUEST = 'pa_request';
    const TERM_URL = 'term_url';
    const HPP_URL = 'hpp_url';
    const BILLING_ADDRESS = 'billing_address';
    const BLOCKED_METHODS = 'blocked_methods';
    const BRAND_CODE = 'brand_code';
    const COUNTRY_CODE = 'country_code';
    const CURRENCY_CODE = 'currency_code';
    const DELIVERY_ADDRESS = 'delivery_address';
    const DF_VALUE = 'df_value';
    const ISSUER_ID = 'issuer_id';
    const MERCHANT_ACCOUNT = 'merchant_account';
    const MERCHANT_REFERENCE = 'merchant_reference';
    const OPEN_INVOICE_DATA = 'open_invoice_data';
    const PAYMENT_AMOUNT = 'payment_amount';
    const RECURRING_CONTRACT = 'recurring_contract';
    const RES_URL = 'res_url';
    const SESSION_VALIDITY = 'session_validity';
    const SHIP_BEFORE_DATE = 'ship_before_date';
    const SHOPPER = 'shopper';
    const SHOPPER_EMAIL = 'shopper_email';
    const SHOPPER_IP = 'shopper_ip';
    const SHOPPER_LOCALE = 'shopper_locale';
    const SHOPPER_REFERENCE = 'shopper_reference';
    const SKIN_CODE = 'skin_code';
    const MERCHANT_SIG = 'merchant_sig';

    /**
     * @param $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setIssuerUrl($value);

    /**
     * @return string | null
     */
    public function getIssuerUrl();

    /**
     * @param $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setMd($value);

    /**
     * @return string | null
     */
    public function getMd();

    /**
     * @param $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setPaRequest($value);

    /**
     * @return string | null
     */
    public function getPaRequest();

    /**
     * @param $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setTermUrl($value);

    /**
     * @return string | null
     */
    public function getTermUrl();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setHppUrl($value);

    /**
     * @return string | null
     */
    public function getHppUrl();

    /**
     * @param \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface $address
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setBillingAddress($address);

    /**
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface
     */
    public function getBillingAddress();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setBlockedMethods($value);

    /**
     * @return string | null
     */
    public function getBlockedMethods();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setBrandCode($value);

    /**
     * @return string | null
     */
    public function getBrandCode();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setCountryCode($value);

    /**
     * @return string | null
     */
    public function getCountryCode();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setCurrencyCode($value);

    /**
     * @return string | null
     */
    public function getCurrencyCode();

    /**
     * @param \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setDeliveryAddress($value);

    /**
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface
     */
    public function getDeliveryAddress();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setDfValue($value);

    /**
     * @return string | null
     */
    public function getDfValue();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setIssuerId($value);

    /**
     * @return string | null
     */
    public function getIssuerId();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setMerchantAccount($value);

    /**
     * @return string | null
     */
    public function getMerchantAccount();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setMerchantReference($value);

    /**
     * @return string | null
     */
    public function getMerchantReference();

    /**
     * @param \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceInterface $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setOpenInvoiceData($value);

    /**
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceInterface
     */
    public function getOpenInvoiceData();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setPaymentAmount($value);

    /**
     * @return string | null
     */
    public function getPaymentAmount();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setRecurringContract($value);

    /**
     * @return string | null
     */
    public function getRecurringContract();   

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setResUrl($value);

    /**
     * @return string | null
     */
    public function getResUrl();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setSessionValidity($value);

    /**
     * @return string | null
     */
    public function getSessionValidity();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setShipBeforeDate($value);

    /**
     * @return string | null
     */
    public function getShipBeforeDate();

    /**
     * @param \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setShopper($value);

    /**
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function getShopper();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setShopperEmail($value);

    /**
     * @return string | null
     */
    public function getShopperEmail();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setShopperIp($value);

    /**
     * @return string | null
     */
    public function getShopperIp();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setShopperLocale($value);

    /**
     * @return string | null
     */
    public function getShopperLocale();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setShopperReference($value);

    /**
     * @return string | null
     */
    public function getShopperReference();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setSkinCode($value);

    /**
     * @return string | null
     */
    public function getSkinCode();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setMerchantSig($value);

    /**
     * @return string | null
     */
    public function getMerchantSig();
}