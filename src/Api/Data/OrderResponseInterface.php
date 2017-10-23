<?php
namespace Hatimeria\Reagento\Api\Data;


interface OrderResponseInterface
{
    const ADYEN_REDIRECT = 'adyen';
    const ORDER_ID = 'order_id';

    /**
     * @param \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface $adyenRedirect
     * @return \Hatimeria\Reagento\Api\Data\OrderResponseInterface
     */
    public function setAdyen(\Hatimeria\Reagento\Api\Data\AdyenRedirectInterface $adyenRedirect);

    /**
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function getAdyen();

    /**
     * @param string $orderId
     * @return \Hatimeria\Reagento\Api\Data\OrderResponseInterface
     */
    public function setOrderId($orderId);

    /**
     * @return string
     */
    public function getOrderId();
}