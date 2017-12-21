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
     * @param \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface[] $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceInterface
     */
    public function setItems($param)
    {
        return $this->setData(self::ITEMS, $param);
    }

    /**
     * @return string
     */
    public function getNumberOfLines()
    {
        return $this->_getData(self::NUMBER_OF_LINES);
    }

    /**
     * @param $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceInterface
     */
    public function setNumberOfLines($param)
    {
        return $this->setData(self::NUMBER_OF_LINES, $param);
    }

    /**
     * @return string
     */
    public function getRefundDescription()
    {
        return $this->_getData(self::REFUND_DESCRIPTION);
    }

    /**
     * @param $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceInterface
     */
    public function setRefundDescription($param)
    {
        return $this->setData(self::REFUND_DESCRIPTION, $param);
    }
}