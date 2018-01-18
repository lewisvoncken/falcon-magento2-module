<?php
namespace Hatimeria\Reagento\Model\Sales\Order;

use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\ShippingAssignmentBuilder;

class Extension
{
    /** @var ExtensionAttributesFactory */
    protected $extensionAttributesFactory;

    /** @var ShippingAssignmentBuilder */
    protected $shippingAssignmentBuilder;

    /**
     * Extension constructor.
     * @param ExtensionAttributesFactory $extensionAttributesFactory
     * @param ShippingAssignmentBuilder $shippingAssignmentBuilder
     */
    public function __construct(
        ExtensionAttributesFactory $extensionAttributesFactory,
        ShippingAssignmentBuilder $shippingAssignmentBuilder
    ) {
        $this->extensionAttributesFactory = $extensionAttributesFactory;
        $this->shippingAssignmentBuilder = $shippingAssignmentBuilder;
    }

    /**
     * Add extension attributes to order entity
     *
     * @param OrderInterface $order
     */
    public function addAttributes(OrderInterface $order)
    {
        $extensionAttributes = $this->getOrderExtensionAttribute($order);

        if (!$extensionAttributes->getShippingAssignments()) {
            /** @var ShippingAssignmentBuilder $shippingAssignment */
            $shippingAssignments = $this->shippingAssignmentBuilder;
            $shippingAssignments->setOrderId($order->getEntityId());

            $extensionAttributes->setShippingAssignments($shippingAssignments->create());
        }
        $order->setExtensionAttributes($extensionAttributes);
    }

    /**
     * @param OrderInterface $order
     * @return \Magento\Sales\Api\Data\OrderExtensionInterface|null|object
     */
    protected function getOrderExtensionAttribute(OrderInterface $order)
    {
        $extensionAttributes = $order->getExtensionAttributes();
        if (!$extensionAttributes) {
            $extensionAttributes = $this->extensionAttributesFactory->create(OrderInterface::class);
        }

        return $extensionAttributes;
    }
}