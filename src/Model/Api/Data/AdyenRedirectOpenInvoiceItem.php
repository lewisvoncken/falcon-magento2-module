<?php
namespace Hatimeria\Reagento\Model\Api\Data;

use Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class AdyenRedirectOpenInvoiceItem extends AbstractExtensibleModel implements AdyenRedirectOpenInvoiceItemInterface
{
    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->_getData(self::CURRENCY_CODE);
    }

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface
     */
    public function setCurrencyCode($value)
    {
        return $this->setData(self::CURRENCY_CODE, $value);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->_getData(self::DESCRIPTION);
    }

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface
     */
    public function setDescription($value)
    {
        return $this->setData(self::DESCRIPTION, $value);
    }

    /**
     * @return integer
     */
    public function getItemAmount()
    {
        return $this->_getData(self::ITEM_AMOUNT);
    }

    /**
     * @param integer $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface
     */
    public function setItemAmount($value)
    {
        return $this->setData(self::ITEM_AMOUNT, $value);
    }

    /**
     * @return integer
     */
    public function getItemVatAmount()
    {
        return $this->_getData(self::ITEM_VAT_AMOUNT);
    }

    /**
     * @param integer $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface
     */
    public function setItemVatAmount($value)
    {
        return $this->setData(self::ITEM_VAT_AMOUNT, $value);
    }

    /**
     * @return string
     */
    public function getItemVatPercentage()
    {
        return $this->_getData(self::ITEM_VAT_PERCENTAGE);
    }

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface
     */
    public function setItemVatPercentage($value)
    {
        return $this->setData(self::ITEM_VAT_PERCENTAGE, $value);
    }

    /**
     * @return integer
     */
    public function getNumberOfItems()
    {
        return $this->_getData(self::NUMBER_OF_ITEMS);
    }

    /**
     * @param integer $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface
     */
    public function setNumberOfItems($value)
    {
        return $this->setData(self::NUMBER_OF_ITEMS, $value);
    }

    /**
     * @return string
     */
    public function getVatCategory()
    {
        return $this->_getData(self::VAT_CATEGORY);
    }

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface
     */
    public function setVatCategory($value)
    {
        return $this->setData(self::VAT_CATEGORY, $value);
    }
}