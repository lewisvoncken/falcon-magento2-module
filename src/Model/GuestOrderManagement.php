<?php

namespace Hatimeria\Reagento\Model;

use Hatimeria\Reagento\Api\HatimeriaGuestOrderManagementInterface;
use Hatimeria\Reagento\Model\Sales\OrderItem\Extension as OrderItemExtension;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class GuestOrderManagement implements HatimeriaGuestOrderManagementInterface
{

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var OrderIdMaskFactory */
    private $orderIdMaskFactory;

    /** @var OrderItemExtension */
    private $orderItemExtension;

    /**
     * GuestOrderManagement constructor.
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderItemExtension $orderItemExtension
     * @param OrderIdMaskFactory $orderIdMaskFactory
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderItemExtension $orderItemExtension,
        OrderIdMaskFactory $orderIdMaskFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderIdMaskFactory = $orderIdMaskFactory;
        $this->orderItemExtension = $orderItemExtension;
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
        $this->addOrderItemExtensionAttributes($order);
        if(!$order->getId() || $order->getCustomerId()) {
            throw new NoSuchEntityException(__('Unable to find order %maskedOrderId', ['maskedOrderId' => $orderId]));
        }

        return $order;
    }

    /**
     * @param OrderInterface $order
     */
    protected function addOrderItemExtensionAttributes(OrderInterface $order)
    {
        foreach($order->getItems() as $item) { /** @var OrderItemInterface $item */
            $this->orderItemExtension->addAttributes($item);
        }
    }
}
