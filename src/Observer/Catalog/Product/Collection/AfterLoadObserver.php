<?php

namespace Hatimeria\Reagento\Observer\Catalog\Product\Collection;

use Hatimeria\Reagento\Helper\Product as HatimeriaProductHelper;
use Hatimeria\Reagento\Helper\Stock as HatimeriaStockHelper;
use Magento\Catalog\Model\Product as Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
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

        foreach ($collection as $item) {
            /** @var Product $item */
            $this->productHelper->ensurePriceForConfigurableProduct($item);
            $this->productHelper->calculateCatalogDisplayPrice($item);
            $this->productHelper->addProductImageAttribute($item, 'product_list_image', 'thumbnail_url');
        }

        $this->stockHelper->addStockDataToCollection($collection);
    }
}
