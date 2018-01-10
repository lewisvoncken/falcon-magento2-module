<?php
namespace Hatimeria\Reagento\Model\Api\Data;

use Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class AdyenRedirectAddress extends AbstractExtensibleModel implements AdyenRedirectAddressInterface
{
    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface
     */
    public function setCity($value)
    {
        return $this->setData(self::CITY, $value);
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->_getData(self::CITY);
    }

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface
     */
    public function setCountry($value)
    {
        return $this->setData(self::COUNTRY, $value);
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->_getData(self::COUNTRY);
    }

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface
     */
    public function setHouseNumberOrName($value)
    {
        return $this->setData(self::HOUSE_NUMBER_OR_NAME, $value);
    }

    /**
     * @return string
     */
    public function getHouseNumberOrName()
    {
        return $this->_getData(self::HOUSE_NUMBER_OR_NAME);
    }

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface
     */
    public function setPostalCode($value)
    {
        return $this->setData(self::POSTAL_CODE, $value);
    }

    /**
     * @return string
     */
    public function getPostalCode()
    {
        return $this->_getData(self::POSTAL_CODE);
    }

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface
     */
    public function setStateOrProvince($value)
    {
        return $this->setData(self::STATE_OR_PROVINCE, $value);
    }

    /**
     * @return string
     */
    public function getStateOrProvince()
    {
        return $this->_getData(self::STATE_OR_PROVINCE);
    }

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface
     */
    public function setStreet($value)
    {
        return $this->setData(self::STREET, $value);
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->_getData(self::STREET);
    }
}