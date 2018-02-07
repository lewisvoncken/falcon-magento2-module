<?php

namespace Hatimeria\Reagento\Plugin\Quote\Model;

use Hatimeria\Reagento\Api\Data\OrderResponseInterface;
use Hatimeria\Reagento\Api\Data\OrderResponseInterfaceFactory;
use Magento\Framework\Event\Manager;
use Magento\Quote\Model\QuoteManagement as MagentoQuoteManagement;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Api\OrderRepositoryInterface;

class QuoteManagement
{
    /** @var OrderRepositoryInterface */
    protected $orderRepository;

    /** @var OrderResponseInterfaceFactory */
    protected $orderResponseFactory;

    /** @var Manager */
    protected $eventManager;

    /**
     * AfterPlaceOrder constructor.
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderResponseInterfaceFactory $orderResponseFactory
     * @param Manager $eventManager
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderResponseInterfaceFactory $orderResponseFactory,
        Manager $eventManager
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderResponseFactory = $orderResponseFactory;
        $this->eventManager = $eventManager;
    }

    /**
     * @param MagentoQuoteManagement $subject
     * @param int $orderId
     * @return OrderResponseInterface | string
     */
    public function afterPlaceOrder(MagentoQuoteManagement $subject, $orderId)
    {
        /** @var OrderInterface|Order $order */
        $order = $this->orderRepository->get($orderId);

        /** @var OrderResponseInterface $obj */
        $obj = $this->orderResponseFactory->create();
        $obj->setOrderId($orderId);
        $obj->setOrderRealId($order->getIncrementId());

        $this->eventManager->dispatch(
            'reagento_place_order_response',
            [
                'response' => $obj,
                'order' => $order
            ]
        );


        return $obj;
    }

}
