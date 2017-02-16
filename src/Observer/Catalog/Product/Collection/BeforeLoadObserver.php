<?php

namespace Hatimeria\Reagento\Observer\Catalog\Product\Collection;

use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class BeforeLoadObserver
 * Purpose: Apply sort order for products (set per category)
 * @package Hatimeria\Reagento\Observer\Catalog\Product\Collection
 */
class BeforeLoadObserver implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        /** @var ProductCollection $collection */
        $collection = $observer->getCollection();
        // Hide duplicates (joined 'catalog_category_product_index' table contains root/default category entries)
        $collection->distinct(true);
        $collection->joinField('position', 'catalog_category_product_index', 'position', 'product_id = entity_id');
        $collection->getSelect()->order('position ASC');
    }
}