<?php

namespace Hatimeria\Reagento\Model;

use Hatimeria\Reagento\Api\HatimeriaOrderManagementInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class GuestOrderManagement implements HatimeriaOrderManagementInterface
{

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var OrderIdMaskFactory */
    private $orderIdMaskFactory;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderIdMaskFactory $orderIdMaskFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderIdMaskFactory = $orderIdMaskFactory;
    }

    /**
     * @param $orderId
     * @return OrderInterface
     * @throws NoSuchEntityException
     */
    public function getItem($orderId)
    {
        $orderIdMask = $this->orderIdMaskFactory->create()->load($orderId, 'masked_id');
        $realOrderId = $orderIdMask->getOrderId();
        if(!$realOrderId) {
            throw new NoSuchEntityException();
        }

        $order = $this->orderRepository->get($realOrderId);
        if(!$order->getId() || $order->getCustomerId()) {
            throw new NoSuchEntityException(__('Unable to find order %maskedOrderId', ['maskedOrderId' => $orderId]));
        }

        return $order;
    }
}