<?php

namespace Hatimeria\Reagento\Model\Plugin;

use Magento\Sales\Model\Order;
use Magento\Sales\Api\OrderRepositoryInterface;

class AfterPlaceOrder
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * AfterPlaceOrder constructor.
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param $subject
     * @param $orderId
     * @return int
     */
    public function afterPlaceOrder($subject, $orderId)
    {
        /** @var Order $order */
        $order = $this->orderRepository->get($orderId);
        return $order->getData('increment_id');
    }
}