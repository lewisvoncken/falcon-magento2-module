<?php
namespace Hatimeria\Reagento\Api\Data;


use Magento\Framework\Api\ExtensibleDataInterface;

interface OrderResponseInterface extends ExtensibleDataInterface
{
    const ADYEN_REDIRECT = 'adyen';
    const ORDER_ID = 'order_id';
    const ORDER_REAL_ID = 'order_real_id';

    /**
     * @param string $orderId
     * @return \Hatimeria\Reagento\Api\Data\OrderResponseInterface
     */
    public function setOrderId($orderId);

    /**
     * @return string
     */
    public function getOrderId();

    /**
     * @param string $incrementId
     * @return \Hatimeria\Reagento\Api\Data\OrderResponseInterface
     */
    public function setOrderRealId($incrementId);

    /**
     * @return string
     */
    public function getOrderRealId();

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Hatimeria\Reagento\Api\Data\OrderResponseExtensionInterface
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Hatimeria\Reagento\Api\Data\OrderResponseExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Hatimeria\Reagento\Api\Data\OrderResponseExtensionInterface $extensionAttributes
    );
}