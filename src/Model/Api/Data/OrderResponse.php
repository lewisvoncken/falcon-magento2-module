<?php
namespace Hatimeria\Reagento\Model\Api\Data;

use Hatimeria\Reagento\Api\Data\OrderResponseInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class OrderResponse extends AbstractExtensibleModel implements OrderResponseInterface
{
    /**
     * @param \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface $adyenRedirect
     * @return \Hatimeria\Reagento\Api\Data\OrderResponseInterface
     */
    public function setAdyen(\Hatimeria\Reagento\Api\Data\AdyenRedirectInterface $adyenRedirect)
    {
        return $this->setData(self::ADYEN_REDIRECT, $adyenRedirect);
    }

    /**
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function getAdyen()
    {
        return $this->_getData(self::ADYEN_REDIRECT);
    }

    /**
     * @param string $orderId
     * @return \Hatimeria\Reagento\Api\Data\OrderResponseInterface
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->_getData(self::ORDER_ID);
    }

    /**
     * @param string $incrementId
     * @return \Hatimeria\Reagento\Api\Data\OrderResponseInterface
     */
    public function setOrderRealId($incrementId)
    {
        return $this->setData(self::ORDER_REAL_ID, $incrementId);
    }

    /**
     * @return string
     */
    public function getOrderRealId()
    {
        return $this->_getData(self::ORDER_REAL_ID);
    }
}