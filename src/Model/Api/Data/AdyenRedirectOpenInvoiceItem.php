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
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface
     */
    public function setCurrencyCode($param)
    {
        return $this->setData(self::CURRENCY_CODE, $param);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->_getData(self::DESCRIPTION);
    }

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface
     */
    public function setDescription($param)
    {
        return $this->setData(self::DESCRIPTION, $param);
    }

    /**
     * @return string
     */
    public function getItemAmount()
    {
        return $this->_getData(self::ITEM_AMOUNT);
    }

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface
     */
    public function setItemAmount($param)
    {
        return $this->setData(self::ITEM_AMOUNT, $param);
    }

    /**
     * @return string
     */
    public function getItemVatAmount()
    {
        return $this->_getData(self::ITEM_VAT_AMOUNT);
    }

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface
     */
    public function setItemVatAmount($param)
    {
        return $this->setData(self::ITEM_VAT_AMOUNT, $param);
    }

    /**
     * @return string
     */
    public function getItemVatPercentage()
    {
        return $this->_getData(self::ITEM_VAT_PERCENTAGE);
    }

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface
     */
    public function setItemVatPercentage($param)
    {
        return $this->setData(self::ITEM_VAT_PERCENTAGE, $param);
    }

    /**
     * @return string
     */
    public function getNumberOfItems()
    {
        return $this->_getData(self::NUMBER_OF_ITEMS);
    }

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface
     */
    public function setNumberOfItems($param)
    {
        return $this->setData(self::NUMBER_OF_ITEMS, $param);
    }

    /**
     * @return string
     */
    public function getVatCategory()
    {
        return $this->_getData(self::VAT_CATEGORY);
    }

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface
     */
    public function setVatCategory($param)
    {
        return $this->setData(self::VAT_CATEGORY, $param);
    }
}