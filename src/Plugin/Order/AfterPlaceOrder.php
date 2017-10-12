<?php

namespace Hatimeria\Reagento\Plugin\Order;

use Hatimeria\Reagento\Model\OrderIdMask;
use Hatimeria\Reagento\Model\OrderIdMaskFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Psr\Log\LoggerInterface;

class AfterPlaceOrder
{
    /** @var OrderIdMaskFactory */
    protected $orderIdMaskFactory;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * AfterPlaceOrder constructor.
     * @param OrderIdMaskFactory $orderIdMaskFactory
     * @param LoggerInterface $logger
     */
    public function __construct(OrderIdMaskFactory $orderIdMaskFactory, LoggerInterface $logger)
    {
        $this->orderIdMaskFactory = $orderIdMaskFactory;
        $this->logger = $logger;
    }

    /**
     * @param OrderManagementInterface $subject
     * @param OrderInterface $result
     * @return OrderInterface
     */
    public function afterPlace(OrderManagementInterface $subject, OrderInterface $result)
    {
        $this->logger->debug('Generate Mask ID plugin');
        if (!$result->getCustomerId()) {
            $this->logger->debug('Generate Mask ID for order ' . $result->getEntityId());
            $this->createMaskedIdForOrder($result);
        }
        return $result;
    }

    /**
     * Create and save new order masked id
     *
     * @param OrderInterface $order
     */
    protected function createMaskedIdForOrder(OrderInterface $order)
    {
        try {
            /** @var OrderIdMask $orderIdMask */
            $orderIdMask = $this->orderIdMaskFactory->create();
            $orderIdMask->setOrderId($order->getEntityId());
            $orderIdMask->getResource()->save($orderIdMask);
            $this->logger->debug('Generated Mask ID: ' . $orderIdMask->getMaskedId());
        } catch (\Exception $e) {
            //order is already saved so do not escalate this exception to not break ordering process
            $this->logger->critical($e->getMessage());
        }
    }
}