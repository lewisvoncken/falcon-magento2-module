<?php
namespace Hatimeria\Reagento\Model\Api\Data;

use Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class AdyenRedirectAddress extends AbstractExtensibleModel implements AdyenRedirectAddressInterface
{
    /**
     * @param string $data
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface
     */
    public function setCity($data)
    {
        return $this->setData(self::CITY, $data);
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->_getData(self::CITY);
    }

    /**
     * @param string $data
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface
     */
    public function setCountry($data)
    {
        return $this->setData(self::COUNTRY, $data);
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->_getData(self::COUNTRY);
    }

    /**
     * @param string $data
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface
     */
    public function setHouseNumberOrName($data)
    {
        return $this->setData(self::HOUSE_NUMBER_OR_NAME, $data);
    }

    /**
     * @return string
     */
    public function getHouseNumberOrName()
    {
        return $this->_getData(self::HOUSE_NUMBER_OR_NAME);
    }

    /**
     * @param string $data
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface
     */
    public function setPostalCode($data)
    {
        return $this->setData(self::POSTAL_CODE, $data);
    }

    /**
     * @return string
     */
    public function getPostalCode()
    {
        return $this->_getData(self::POSTAL_CODE);
    }

    /**
     * @param string $data
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface
     */
    public function setStateOrProvince($data)
    {
        return $this->setData(self::STATE_OR_PROVINCE, $data);
    }

    /**
     * @return string
     */
    public function getStateOrProvince()
    {
        return $this->_getData(self::STATE_OR_PROVINCE);
    }

    /**
     * @param string $data
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface
     */
    public function setStreet($data)
    {
        return $this->setData(self::STREET, $data);
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->_getData(self::STREET);
    }
}