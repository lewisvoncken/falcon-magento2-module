<?php
namespace Hatimeria\Reagento\Model\Api\Data;

use Hatimeria\Reagento\Api\Data\AdyenRedirectInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class AdyenRedirect extends AbstractExtensibleModel implements AdyenRedirectInterface
{
    /**
     * @param $param
     * @return AdyenRedirectInterface
     */
    public function setIssuerUrl($param)
    {
        return $this->setData(self::ISSUER_URL, $param);
    }

    /**
     * @return string
     */
    public function getIssuerUrl()
    {
        return $this->_getData(self::ISSUER_URL);
    }

    /**
     * @param $param
     * @return AdyenRedirectInterface
     */
    public function setMd($param)
    {
        return $this->setData(self::MD, $param);
    }

    /**
     * @return string
     */
    public function getMd()
    {
        return $this->_getData(self::MD);
    }

    /**
     * @param $param
     * @return AdyenRedirectInterface
     */
    public function setPaRequest($param)
    {
        return $this->setData(self::PA_REQUEST, $param);
    }

    /**
     * @return string
     */
    public function getPaRequest()
    {
        return $this->_getData(self::PA_REQUEST);
    }

    /**
     * @param $param
     * @return AdyenRedirectInterface
     */
    public function setTermUrl($param)
    {
        return $this->setData(self::TERM_URL, $param);
    }

    /**
     * @return string
     */
    public function getTermUrl()
    {
        return $this->_getData(self::TERM_URL);
    }

    /**
     * @param string $param
     * @return AdyenRedirectInterface
     */
    public function setHppUrl($param)
    {
        return $this->setData(self::HPP_URL, $param);
    }

    /**
     * @return string | null
     */
    public function getHppUrl()
    {
        return $this->getData(self::HPP_URL);
    }

    /**
     * @param \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface $address
     * @return AdyenRedirectInterface
     */
    public function setBillingAddress($address)
    {
        return $this->setData(self::BILLING_ADDRESS, $address);
    }

    /**
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface
     */
    public function getBillingAddress()
    {
        return $this->_getData(self::BILLING_ADDRESS);
    }

    /**
     * @param string $param
     * @return AdyenRedirectInterface
     */
    public function setBlockedMethods($param)
    {
        return $this->setData(self::BLOCKED_METHODS, $param);
    }

    /**
     * @return string
     */
    public function getBlockedMethods()
    {
        return $this->_getData(self::BLOCKED_METHODS);
    }

    /**
     * @param string $param
     * @return AdyenRedirectInterface
     */
    public function setBrandCode($param)
    {
        return $this->setData(self::BRAND_CODE, $param);
    }

    /**
     * @return string
     */
    public function getBrandCode()
    {
        return $this->_getData(self::BRAND_CODE);
    }

    /**
     * @param string $param
     * @return AdyenRedirectInterface
     */
    public function setCountryCode($param)
    {
        return $this->setData(self::COUNTRY_CODE, $param);
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->_getData(self::COUNTRY_CODE);
    }

    /**
     * @param string $param
     * @return AdyenRedirectInterface
     */
    public function setCurrencyCode($param)
    {
        return $this->setData(self::CURRENCY_CODE, $param);
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->_getData(self::CURRENCY_CODE);
    }

    /**
     * @param \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface $param
     * @return AdyenRedirectInterface
     */
    public function setDeliveryAddress($param)
    {
        return $this->setData(self::DELIVERY_ADDRESS, $param);
    }

    /**
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface
     */
    public function getDeliveryAddress()
    {
        return $this->_getData(self::DELIVERY_ADDRESS);
    }

    /**
     * @param string $param
     * @return AdyenRedirectInterface
     */
    public function setDfValue($param)
    {
        return $this->setData(self::DF_VALUE, $param);
    }

    /**
     * @return string
     */
    public function getDfValue()
    {
        return $this->_getData(self::DF_VALUE);
    }

    /**
     * @param string $param
     * @return AdyenRedirectInterface
     */
    public function setIssuerId($param)
    {
        return $this->setData(self::ISSUER_ID, $param);
    }

    /**
     * @return string
     */
    public function getIssuerId()
    {
        return $this->_getData(self::ISSUER_ID);
    }

    /**
     * @param string $param
     * @return AdyenRedirectInterface
     */
    public function setMerchantAccount($param)
    {
        return $this->setData(self::MERCHANT_ACCOUNT, $param);
    }

    /**
     * @return string
     */
    public function getMerchantAccount()
    {
        return $this->_getData(self::MERCHANT_ACCOUNT);
    }

    /**
     * @param string $param
     * @return AdyenRedirectInterface
     */
    public function setMerchantReference($param)
    {
        return $this->setData(self::MERCHANT_REFERENCE, $param);
    }

    /**
     * @return string
     */
    public function getMerchantReference()
    {
        return $this->_getData(self::MERCHANT_REFERENCE);
    }

    /**
     * @param \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceInterface $param
     * @return AdyenRedirectInterface
     */
    public function setOpenInvoiceData($param)
    {
        return $this->setData(self::OPEN_INVOICE_DATA, $param);
    }

    /**
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceInterface
     */
    public function getOpenInvoiceData()
    {
        return $this->_getData(self::OPEN_INVOICE_DATA);
    }

    /**
     * @param string $param
     * @return AdyenRedirectInterface
     */
    public function setPaymentAmount($param)
    {
        return $this->setData(self::PAYMENT_AMOUNT, $param);
    }

    /**
     * @return string
     */
    public function getPaymentAmount()
    {
        return $this->_getData(self::PAYMENT_AMOUNT);
    }

    /**
     * @param string $value
     * @return AdyenRedirectInterface
     */
    public function setRecurringContract($value)
    {
        return $this->setData(self::RECURRING_CONTRACT, $value);
    }

    public function getRecurringContract()
    {
        return $this->_getData(self::RECURRING_CONTRACT);
    }

    /**
     * @param string $param
     * @return AdyenRedirectInterface
     */
    public function setResUrl($param)
    {
        return $this->setData(self::RES_URL, $param);
    }

    /**
     * @return string
     */
    public function getResUrl()
    {
        return $this->_getData(self::RES_URL);
    }

    /**
     * @param string $param
     * @return AdyenRedirectInterface
     */
    public function setSessionValidity($param)
    {
        return $this->setData(self::SESSION_VALIDITY, $param);
    }

    /**
     * @return string
     */
    public function getSessionValidity()
    {
        return $this->_getData(self::SESSION_VALIDITY);
    }

    /**
     * @param string $param
     * @return AdyenRedirectInterface
     */
    public function setShipBeforeDate($param)
    {
        return $this->setData(self::SHIP_BEFORE_DATE, $param);
    }

    /**
     * @return string
     */
    public function getShipBeforeDate()
    {
        return $this->_getData(self::SHIP_BEFORE_DATE);
    }

    /**
     * @param \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface $param
     * @return AdyenRedirectInterface
     */
    public function setShopper($param)
    {
        return $this->setData(self::SHOPPER, $param);
    }

    /**
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function getShopper()
    {
        return $this->_getData(self::SHOPPER);
    }

    /**
     * @param string $param
     * @return AdyenRedirectInterface
     */
    public function setShopperEmail($param)
    {
        return $this->setData(self::SHOPPER_EMAIL, $param);
    }

    /**
     * @return string
     */
    public function getShopperEmail()
    {
        return $this->_getData(self::SHOPPER_EMAIL);
    }

    /**
     * @param string $param
     * @return AdyenRedirectInterface
     */
    public function setShopperIp($param)
    {
        return $this->setData(self::SHOPPER_IP, $param);
    }

    /**
     * @return string
     */
    public function getShopperIp()
    {
        return $this->_getData(self::SHOPPER_IP);
    }

    /**
     * @param string $param
     * @return AdyenRedirectInterface
     */
    public function setShopperLocale($param)
    {
        return $this->setData(self::SHOPPER_LOCALE, $param);
    }

    /**
     * @return string
     */
    public function getShopperLocale()
    {
        return $this->_getData(self::SHOPPER_LOCALE);
    }

    /**
     * @param string $param
     * @return AdyenRedirectInterface
     */
    public function setShopperReference($param)
    {
        return $this->setData(self::SHOPPER_REFERENCE, $param);
    }

    /**
     * @return string
     */
    public function getShopperReference()
    {
        return $this->_getData(self::SHOPPER_REFERENCE);
    }

    /**
     * @param string $param
     * @return AdyenRedirectInterface
     */
    public function setSkinCode($param)
    {
        return $this->setData(self::SKIN_CODE, $param);
    }

    /**
     * @return string
     */
    public function getSkinCode()
    {
        return $this->_getData(self::SKIN_CODE);
    }

    /**
     * @param string $param
     * @return AdyenRedirectInterface
     */
    public function setMerchantSig($param)
    {
        return $this->setData(self::MERCHANT_SIG, $param);
    }

    /**
     * @return string
     */
    public function getMerchantSig()
    {
        return $this->_getData(self::MERCHANT_SIG);
    }
}