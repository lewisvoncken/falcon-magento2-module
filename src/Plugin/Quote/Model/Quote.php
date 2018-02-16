<?php
namespace Deity\MagentoApi\Plugin\Quote\Model;

use Magento\Quote\Model\Quote as QuoteModel;

class Quote
{
    /**
     * Create shipping address object for non-virtual quotes
     *
     * @param QuoteModel $quote
     */
    public function beforeCollectTotals(QuoteModel $quote)
    {
        if (!$quote->isVirtual()) {
            //make sure default shipping address object exists, otherwise first item added to cart for logged in customer
            //won't have totals correctly calculated
            $quote->getShippingAddress();
        }
    }
}