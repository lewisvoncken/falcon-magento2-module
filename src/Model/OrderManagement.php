<?php

namespace Hatimeria\Reagento\Model;

use Hatimeria\Reagento\Api\HatimeriaOrderManagementInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class OrderManagement implements HatimeriaOrderManagementInterface
{

    /** @var OrderRepositoryInterface */
    protected $orderRepository;

    /** @var UserContextInterface */
    protected $userContext;

    /**
     * OrderManagement constructor.
     * @param OrderRepositoryInterface $orderRepository
     * @param UserContextInterface $userContext
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        UserContextInterface $userContext
    ) {
        $this->orderRepository = $orderRepository;
        $this->userContext = $userContext;
    }

    /**
     * @param $orderId
     * @return OrderInterface
     * @throws NoSuchEntityException
     */
    public function getItem($orderId)
    {
        $order = $this->orderRepository->get($orderId);
        if(!$order->getId() || $order->getCustomerId() !== $this->userContext->getUserId()) {
            throw new NoSuchEntityException(__('Unable to find order %orderId', ['orderId' => $orderId]));
        }

        return $order;
    }
}