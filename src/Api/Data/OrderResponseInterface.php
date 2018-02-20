<?php
namespace Deity\MagentoApi\Api\Data;


use Magento\Framework\Api\ExtensibleDataInterface;

interface OrderResponseInterface extends ExtensibleDataInterface
{
    const ADYEN_REDIRECT = 'adyen';
    const ORDER_ID = 'order_id';
    const ORDER_REAL_ID = 'order_real_id';

    /**
     * @param string $orderId
     * @return \Deity\MagentoApi\Api\Data\OrderResponseInterface
     */
    public function setOrderId($orderId);

    /**
     * @return string
     */
    public function getOrderId();

    /**
     * @param string $incrementId
     * @return \Deity\MagentoApi\Api\Data\OrderResponseInterface
     */
    public function setOrderRealId($incrementId);

    /**
     * @return string
     */
    public function getOrderRealId();

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Deity\MagentoApi\Api\Data\OrderResponseExtensionInterface
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Deity\MagentoApi\Api\Data\OrderResponseExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Deity\MagentoApi\Api\Data\OrderResponseExtensionInterface $extensionAttributes
    );
}