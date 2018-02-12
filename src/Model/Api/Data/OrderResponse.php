<?php
namespace Deity\MagentoApi\Model\Api\Data;

use Deity\MagentoApi\Api\Data\OrderResponseInterface;
use Deity\MagentoApi\Api\Data\OrderResponseExtensionInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class OrderResponse extends AbstractExtensibleModel implements OrderResponseInterface
{
    /**
     * @param string $orderId
     * @return \Deity\MagentoApi\Api\Data\OrderResponseInterface
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
     * @return \Deity\MagentoApi\Api\Data\OrderResponseInterface
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

    /**
     * @return \Deity\MagentoApi\Api\Data\OrderResponseExtensionInterface
     */
    public function getExtensionAttributes()
    {
        $extensionAttributes = $this->_getExtensionAttributes();
        if (!$extensionAttributes) {
            /** @var OrderResponseExtensionInterface $extensionAttributes */
            $extensionAttributes = $this->extensionAttributesFactory->create(OrderResponseInterface::class);
        }

        return $extensionAttributes;
    }

    /**
     * @param \Deity\MagentoApi\Api\Data\OrderResponseExtensionInterface $extensionAttributes
     * @return \Deity\MagentoApi\Api\Data\OrderResponseInterface
     */
    public function setExtensionAttributes(OrderResponseExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}