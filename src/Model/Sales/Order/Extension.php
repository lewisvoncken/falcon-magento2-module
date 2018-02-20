<?php
namespace Deity\MagentoApi\Model\Sales\Order;

use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\ShippingAssignmentBuilder;

class Extension
{
    /** @var ExtensionAttributesFactory */
    protected $extensionAttributesFactory;

    /** @var ShippingAssignmentBuilder */
    protected $shippingAssignmentBuilder;

    /** @var PriceCurrencyInterface */
    protected $priceCurrency;

    /**
     * Extension constructor.
     * @param ExtensionAttributesFactory $extensionAttributesFactory
     * @param ShippingAssignmentBuilder $shippingAssignmentBuilder
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        ExtensionAttributesFactory $extensionAttributesFactory,
        ShippingAssignmentBuilder $shippingAssignmentBuilder,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->extensionAttributesFactory = $extensionAttributesFactory;
        $this->shippingAssignmentBuilder = $shippingAssignmentBuilder;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * Add extension attributes to order entity
     *
     * @param Order|OrderInterface $order
     */
    public function addAttributes(OrderInterface $order)
    {
        $extensionAttributes = $this->getOrderExtensionAttribute($order);

        $orderCurrency = $this->priceCurrency->getCurrencySymbol($order->getStoreId(), $order->getOrderCurrencyCode());
        $extensionAttributes->setCurrency($orderCurrency ?: $order->getOrderCurrencyCode());

        if (!$order->getIsVirtual()) {
            $extensionAttributes->setShippingAddress($order->getShippingAddress());
        }

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