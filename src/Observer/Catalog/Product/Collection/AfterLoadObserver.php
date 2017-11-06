<?php

namespace Hatimeria\Reagento\Observer\Catalog\Product\Collection;

use Hatimeria\Reagento\Helper\Product as HatimeriaProductHelper;
use Hatimeria\Reagento\Helper\Stock as HatimeriaStockHelper;
use Magento\Catalog\Model\Product as Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\ResourceModel\Product\Compare\Item\Collection as CompareProductItemCollection;
use Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection as ProductLinkCollection;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * @package Hatimeria\Reagento\Observer
 */
class AfterLoadObserver implements ObserverInterface
{
    /** @var HatimeriaProductHelper */
    protected $productHelper;

    /** @var HatimeriaStockHelper */
    protected $stockHelper;

    /**
     * @param HatimeriaProductHelper $productHelper
     * @param HatimeriaStockHelper $stockHelper
     */
    public function __construct(
        HatimeriaProductHelper $productHelper,
        HatimeriaStockHelper $stockHelper
    ) {
        $this->productHelper = $productHelper;
        $this->stockHelper = $stockHelper;
    }

    /**
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        /** @var ProductCollection $collection */
        $collection = $observer->getEvent()->getCollection();

        // hey, let's extend basic product collection class but do not redefine event prefixes
        // so every event will be fired for us as well
        // cause it's better than require developer to make 3 entries in xml events file
        if (
            $collection instanceof ProductLinkCollection
            || $collection instanceof CompareProductItemCollection
        ) {
            return;
        }

        foreach ($collection as $item) {
            /** @var Product $item */
            $item->setProductLinks([]);   //improve speed when building output array
            $this->productHelper->ensurePriceForConfigurableProduct($item);
            $this->productHelper->calculateCatalogDisplayPrice($item);
            $this->productHelper->addProductImageAttribute($item, 'product_list_image', 'thumbnail_url');
        }

        $this->stockHelper->addStockDataToCollection($collection);
    }
}
