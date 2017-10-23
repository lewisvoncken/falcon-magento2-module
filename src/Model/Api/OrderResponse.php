<?php
namespace Hatimeria\Reagento\Model\Api;

use Magento\Framework\DataObject;

class OrderResponse extends DataObject
{
    /**
     * @var \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    protected $adyen;

    /**
     * @var string
     */
    protected $orderId;

    /**
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function getAdyen()
    {
        if (isset($this->_data['adyen'])) {
            return $this->_data['adyen'];
        } else {
            return null;
        }
    }

    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->_data['order_id'];
    }

    /**
     * @param string $key
     * @param null $index
     * @return array
     */
    public function getData($key = '', $index = null)
    {
        return [];
    }
}