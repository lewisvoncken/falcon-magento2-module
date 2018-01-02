<?php
namespace Hatimeria\Reagento\Model\Api\Data;

use Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class AdyenRedirectShopper extends AbstractExtensibleModel implements AdyenRedirectShopperInterface
{
    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function setFirstName($value)
    {
        return $this->setData(self::FIRST_NAME, $value);
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->_getData(self::FIRST_NAME);
    }

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function setGender($value)
    {
        return $this->setData(self::GENDER, $value);
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->_getData(self::GENDER);
    }

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function setInfix($value)
    {
        return $this->setData(self::INFIX, $value);
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
    public function setLastName($value)
    {
        return $this->setData(self::LAST_NAME, $value);
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->_getData(self::LAST_NAME);
    }

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function setTelephoneNumber($value)
    {
        return $this->setData(self::TELEPHONE_NUMBER, $value);
    }

    /**
     * @return string
     */
    public function getTelephoneNumber()
    {
        return $this->_getData(self::TELEPHONE_NUMBER);
    }

    /**
     * @param integer $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function setDateOfBirthDayOfMonth($value)
    {
        return $this->setData(self::DATE_OF_BIRTH_DAY_OF_MONTH, $value);
    }

    /**
     * @return integer
     */
    public function getDateOfBirthDayOfMonth()
    {
        return $this->_getData(self::DATE_OF_BIRTH_DAY_OF_MONTH);
    }

    /**
     * @param integer $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function setDateOfBirthMonth($value)
    {
        return $this->setData(self::DATE_OF_BIRTH_MONTH, $value);
    }

    /**
     * @return integer
     */
    public function getDateOfBirthMonth()
    {
        return $this->_getData(self::DATE_OF_BIRTH_MONTH);
    }

    /**
     * @param integer $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function setDateOfBirthYear($value)
    {
        return $this->setData(self::DATE_OF_BIRTH_YEAR, $value);
    }

    /**
     * @return integer
     */
    public function getDateOfBirthYear()
    {
        return $this->_getData(self::DATE_OF_BIRTH_YEAR);
    }
}