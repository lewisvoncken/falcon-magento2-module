<?php
namespace Hatimeria\Reagento\Model\Api\Data;

use Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class AdyenRedirectShopper extends AbstractExtensibleModel implements AdyenRedirectShopperInterface
{
    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function setFirstName($param)
    {
        return $this->setData(self::FIRST_NAME, $param);
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->_getData(self::FIRST_NAME);
    }

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function setGender($param)
    {
        return $this->setData(self::GENDER, $param);
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->_getData(self::GENDER);
    }

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function setInfix($param)
    {
        return $this->setData(self::INFIX, $param);
    }

    /**
     * @return mixed|string
     */
    public function getInfix()
    {
        return $this->_getData(self::INFIX);
    }

    /**
     * @param string $_getData (self::self::INFIXp;aram
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function setLastName($param)
    {
        return $this->setData(self::LAST_NAME, $param);
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->_getData(self::LAST_NAME);
    }

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function setTelephoneNumber($param)
    {
        return $this->setData(self::TELEPHONE_NUMBER, $param);
    }

    /**
     * @return string
     */
    public function getTelephoneNumber()
    {
        return $this->_getData(self::TELEPHONE_NUMBER);
    }

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function setDateOfBirthDayOfMonth($param)
    {
        return $this->setData(self::DATE_OF_BIRTH_DAY_OF_MONTH, $param);
    }

    /**
     * @return string
     */
    public function getDateOfBirthDayOfMonth()
    {
        return $this->_getData(self::DATE_OF_BIRTH_DAY_OF_MONTH);
    }

    /**
     * @param string$param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function setDateOfBirthMonth($param)
    {
        return $this->setData(self::DATE_OF_BIRTH_MONTH, $param);
    }

    /**
     * @return string
     */
    public function getDateOfBirthMonth()
    {
        return $this->_getData(self::DATE_OF_BIRTH_MONTH);
    }

    /**
     * @param string$param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function setDateOfBirthYear($param)
    {
        return $this->setData(self::DATE_OF_BIRTH_YEAR, $param);
    }

    /**
     * @return string
     */
    public function getDateOfBirthYear()
    {
        return $this->_getData(self::DATE_OF_BIRTH_YEAR);
    }
}