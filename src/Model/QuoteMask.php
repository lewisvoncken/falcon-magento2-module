<?php

namespace Hatimeria\Reagento\Model;

use Hatimeria\Reagento\Api\QuoteMaskInterface;
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

    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        ObjectManagerInterface $objectManager
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->orderFactory = $orderFactory;
        $this->objectManager = $objectManager;
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
        $result = $this->objectManager->create('\Hatimeria\Reagento\Api\Data\OrderInfoInterface');
        $result->setOrderId($order->getRealOrderId());
        $result->setRevenue($order->getGrandTotal());
        $result->setShipping($order->getShippingInclTax());
        $result->setTax($order->getTaxAmount());
        $result->setQuoteId($quoteId);

        return $result;
    }
}