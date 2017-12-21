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
     * @param $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setIssuerUrl($param);

    /**
     * @return string | null
     */
    public function getIssuerUrl();

    /**
     * @param $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setMd($param);

    /**
     * @return string | null
     */
    public function getMd();

    /**
     * @param $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setPaRequest($param);

    /**
     * @return string | null
     */
    public function getPaRequest();

    /**
     * @param $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setTermUrl($param);

    /**
     * @return string | null
     */
    public function getTermUrl();

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setHppUrl($param);

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
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setBlockedMethods($param);

    /**
     * @return string | null
     */
    public function getBlockedMethods();

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setBrandCode($param);

    /**
     * @return string | null
     */
    public function getBrandCode();

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setCountryCode($param);

    /**
     * @return string | null
     */
    public function getCountryCode();

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setCurrencyCode($param);

    /**
     * @return string | null
     */
    public function getCurrencyCode();

    /**
     * @param \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setDeliveryAddress($param);

    /**
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface
     */
    public function getDeliveryAddress();

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setDfValue($param);

    /**
     * @return string | null
     */
    public function getDfValue();

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setIssuerId($param);

    /**
     * @return string | null
     */
    public function getIssuerId();

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setMerchantAccount($param);

    /**
     * @return string | null
     */
    public function getMerchantAccount();

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setMerchantReference($param);

    /**
     * @return string | null
     */
    public function getMerchantReference();

    /**
     * @param \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceInterface $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setOpenInvoiceData($param);

    /**
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceInterface
     */
    public function getOpenInvoiceData();

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setPaymentAmount($param);

    /**
     * @return string | null
     */
    public function getPaymentAmount();

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setResUrl($param);

    /**
     * @return string | null
     */
    public function getResUrl();

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setSessionValidity($param);

    /**
     * @return string | null
     */
    public function getSessionValidity();

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setShipBeforeDate($param);

    /**
     * @return string | null
     */
    public function getShipBeforeDate();

    /**
     * @param \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setShopper($param);

    /**
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function getShopper();

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setShopperEmail($param);

    /**
     * @return string | null
     */
    public function getShopperEmail();

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setShopperIp($param);

    /**
     * @return string | null
     */
    public function getShopperIp();

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setShopperLocale($param);

    /**
     * @return string | null
     */
    public function getShopperLocale();

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setShopperReference($param);

    /**
     * @return string | null
     */
    public function getShopperReference();

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setSkinCode($param);

    /**
     * @return string | null
     */
    public function getSkinCode();

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setMerchantSig($param);

    /**
     * @return string | null
     */
    public function getMerchantSig();
}