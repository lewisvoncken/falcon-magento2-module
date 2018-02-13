<?php

namespace Deity\MagentoApi\Model;

use Deity\MagentoApi\Api\Data\OrderInfoInterface;
use Magento\Framework\Model\AbstractModel;

class OrderInfo extends AbstractModel implements OrderInfoInterface
{
    const KEY_ORDER_ID = 'order_id';
    const KEY_REVENUE = 'revenue';
    const KEY_SHIPPING = 'shipping';
    const KEY_TAX = 'tax';
    const KEY_QUOTE_ID = 'quote_id';
    const KEY_MASKED_ORDER_ID = 'masked_order_id';

    public function getOrderId()
    {
        return $this->getData(self::KEY_ORDER_ID);
    }

    public function setOrderId($id)
    {
        return $this->setData(self::KEY_ORDER_ID, $id);
    }

    public function getRevenue()
    {
        return $this->getData(self::KEY_REVENUE);
    }

    public function setRevenue($revenue)
    {
        return $this->setData(self::KEY_REVENUE, $revenue);
    }

    public function getShipping()
    {
        return $this->getData(self::KEY_SHIPPING);
    }

    public function setShipping($shipping)
    {
        return $this->setData(self::KEY_SHIPPING, $shipping);
    }

    public function getTax()
    {
        return $this->getData(self::KEY_TAX);
    }

    public function setTax($tax)
    {
        return $this->setData(self::KEY_TAX, $tax);
    }

    public function getQuoteId()
    {
        return $this->getData(self::KEY_QUOTE_ID);
    }

    public function setQuoteId($id)
    {
        return $this->setData(self::KEY_QUOTE_ID, $id);
    }

    public function getMaskedId()
    {
        return $this->getData(self::KEY_MASKED_ORDER_ID);
    }

    public function setMaskedId($id)
    {
        return $this->setData(self::KEY_MASKED_ORDER_ID, $id);
    }
}