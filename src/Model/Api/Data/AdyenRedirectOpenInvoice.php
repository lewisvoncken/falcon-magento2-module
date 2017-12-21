<?php
namespace Hatimeria\Reagento\Model\Api\Data;

use Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class AdyenRedirectOpenInvoice extends AbstractExtensibleModel implements AdyenRedirectOpenInvoiceInterface
{
    /**
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface[]
     */
    public function getItems()
    {
        return $this->_getData(self::ITEMS);
    }

    /**
     * @param \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface[] $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceInterface
     */
    public function setItems($value)
    {
        return $this->setData(self::ITEMS, $value);
    }

    /**
     * @return integer
     */
    public function getNumberOfLines()
    {
        return $this->_getData(self::NUMBER_OF_LINES);
    }

    /**
     * @param integer $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceInterface
     */
    public function setNumberOfLines($value)
    {
        return $this->setData(self::NUMBER_OF_LINES, $value);
    }

    /**
     * @return string
     */
    public function getRefundDescription()
    {
        return $this->_getData(self::REFUND_DESCRIPTION);
    }

    /**
     * @param $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceInterface
     */
    public function setRefundDescription($value)
    {
        return $this->setData(self::REFUND_DESCRIPTION, $value);
    }
}