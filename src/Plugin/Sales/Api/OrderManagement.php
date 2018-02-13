<?php

namespace Deity\MagentoApi\Plugin\Sales\Api;

use Deity\MagentoApi\Model\OrderIdMask;
use Deity\MagentoApi\Model\OrderIdMaskFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Psr\Log\LoggerInterface;

class OrderManagement
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
        if (!$result->getCustomerId()) {
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
        } catch (\Exception $e) {
            //order is already saved so do not escalate this exception to not break ordering process
            $this->logger->critical($e->getMessage());
        }
    }
}