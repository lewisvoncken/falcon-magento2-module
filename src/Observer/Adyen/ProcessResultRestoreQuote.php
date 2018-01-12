<?php
namespace Hatimeria\Reagento\Observer\Adyen;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;

class ProcessResultRestoreQuote implements ObserverInterface
{
    const REGISTRY_KEY = 'restored_quote_id';

    /** @var Registry */
    private $registry;

    /** @var QuoteIdMaskFactory */
    private $quoteIdMaskFactory;

    /**
     * ProcessResultRestoreQuote constructor.
     * @param Registry $registry
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     */
    public function __construct(Registry $registry, QuoteIdMaskFactory $quoteIdMaskFactory)
    {
        $this->registry = $registry;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
    }

    public function execute(Observer $observer)
    {
        /** @var CartInterface $quote */
        $quote = $observer->getEvent()->getQuote();

        $quoteId = $quote->getId();

        if (!$quote->getCustomer()->getId()) {
            /** @var QuoteIdMask $quoteIdMask */
            $quoteIdMask = $this->quoteIdMaskFactory->create();
            $quoteIdMask->load($quote->getId(), 'quote_id');
            $quoteId = $quoteIdMask->getMaskedId();
        }

        $this->registry->register(self::REGISTRY_KEY, $quoteId);
    }
}