<?php

namespace Deity\MagentoApi\Observer\Order;

use Deity\MagentoApi\Model\OrderIdMaskFactory;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderExtensionInterface;
use Magento\Sales\Api\Data\OrderInterface;

class SetMaskedId implements ObserverInterface
{
    /** @var OrderIdMaskFactory */
    protected $orderIdMaskFactory;

    /** @var OrderExtensionFactory */
    protected $orderExtensionFactory;

    /**
     * SetMaskedId constructor.
     * @param OrderIdMaskFactory $orderIdMaskFactory
     * @param OrderExtensionFactory $orderExtensionFactory
     */
    public function __construct(
        OrderIdMaskFactory $orderIdMaskFactory,
        OrderExtensionFactory $orderExtensionFactory
    )
    {
        $this->orderIdMaskFactory = $orderIdMaskFactory;
        $this->orderExtensionFactory = $orderExtensionFactory;
    }

    /**
     * Execute Observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var OrderInterface $order */
        $order = $observer->getEvent()->getOrder();

        if ($order->getCustomerId()) {
            return;
        }

        /** @var OrderExtensionInterface $extensionAttributes */
        $extensionAttributes = $order->getExtensionAttributes();
        if (!$extensionAttributes) {
            $extensionAttributes = $this->orderExtensionFactory->create();
        }
        if (!$extensionAttributes->getMaskedId()) {
            $extensionAttributes->setMaskedId($this->getMaskedOrderId($order->getEntityId()));
            $order->setExtensionAttributes($extensionAttributes);
        }
    }

    /**
     * @param int $orderId
     * @return string
     */
    protected function getMaskedOrderId($orderId)
    {
        $maskedId = $this->orderIdMaskFactory->create();
        $maskedId ->getResource()->load($maskedId, $orderId, 'order_id');
        return $maskedId->getMaskedId();
    }
}
