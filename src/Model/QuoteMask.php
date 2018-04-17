<?php

namespace Deity\MagentoApi\Model;

use Deity\MagentoApi\Api\Data\OrderInfoInterface;
use Deity\MagentoApi\Api\QuoteMaskInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Sales\Model\Order;

class QuoteMask implements QuoteMaskInterface
{
    /** @var QuoteIdMaskFactory */
    protected $quoteIdMaskFactory;

    /** @var \Magento\Sales\Model\OrderFactory */
    protected $orderFactory;

    /** @var ObjectManagerInterface */
    protected $objectManager;

    /** @var OrderIdMaskFactory */
    protected $orderIdMaskFactory;

    /**
     * QuoteMask constructor.
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param OrderIdMaskFactory $orderIdMaskFactory
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        OrderIdMaskFactory$orderIdMaskFactory,
        ObjectManagerInterface $objectManager
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->orderFactory = $orderFactory;
        $this->objectManager = $objectManager;
        $this->orderIdMaskFactory = $orderIdMaskFactory;
    }

    /**
     * @param string $cartId
     * @return QuoteIdMask
     */
    protected function getQuoteByMask($cartId)
    {
        return $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
    }

    /**
     * @param string $quoteId
     * @return Order
     */
    protected function getOrderByQuote($quoteId)
    {
        return $this->orderFactory->create()->load($quoteId, 'quote_id');
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

    public function getItem($quoteId)
    {
        $quoteIdMask = $this->getQuoteByMask($quoteId);
        $realQuoteId = $quoteIdMask->getQuoteId();
        if(!$realQuoteId) {
            throw new NoSuchEntityException();
        }

        $order = $this->getOrderByQuote($realQuoteId);
        if(!$order->getId()) {
            throw new NoSuchEntityException();
        }

        /** @var OrderInfo $result */
        $result = $this->objectManager->create(OrderInfoInterface::class);
        $result->setOrderId($order->getRealOrderId());
        $result->setRevenue($order->getGrandTotal());
        $result->setShipping($order->getShippingInclTax());
        $result->setTax($order->getTaxAmount());
        $result->setQuoteId($quoteId);
        $result->setMaskedId($this->getMaskedOrderId($order->getEntityId()));

        return $result;
    }
}