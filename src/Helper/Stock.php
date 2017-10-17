<?php

namespace Hatimeria\Reagento\Helper;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockItemCriteriaInterface;
use Magento\CatalogInventory\Api\StockItemCriteriaInterfaceFactory;
use Magento\CatalogInventory\Api\StockItemRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

class Stock
{
    /** @var StockItemRepositoryInterface */
    protected $stockRepository;

    /** @var StockItemCriteriaInterfaceFactory */
    protected $stockCriteriaInterfaceFactory;

    /** @var StoreManagerInterface */
    protected $storeManager;

    /**
     * Stock constructor.
     * @param StockItemRepositoryInterface $stockRepository
     * @param StockItemCriteriaInterfaceFactory $stockCriteriaInterfaceFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StockItemRepositoryInterface $stockRepository,
        StockItemCriteriaInterfaceFactory $stockCriteriaInterfaceFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->stockRepository = $stockRepository;
        $this->stockCriteriaInterfaceFactory = $stockCriteriaInterfaceFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Add stock item to product collection
     *
     * @param ProductCollection $collection
     */
    public function addStockDataToCollection(ProductCollection $collection)
    {
        $stockCollection = $this->getStockForCollection($collection);
        foreach ($collection as $item) { /** @var ProductInterface $item */
            if (array_key_exists($item->getId(), $stockCollection)) {
                $extensionAttributes = $item->getExtensionAttributes();
                $extensionAttributes->setStockItem($stockCollection[$item->getId()]);
                $item->setExtensionAttributes($extensionAttributes);
            }
        }
    }

    /**
     * @param ProductCollection $collection
     * @return StockItemInterface[]
     */
    protected function getStockForCollection(ProductCollection $collection)
    {
        $websiteId = $this->storeManager->getStore($collection->getStoreId())->getWebsiteId();
        $productIds = $collection->getLoadedIds();
        /** @var StockItemCriteriaInterface $searchCriteria */
        $searchCriteria = $this->stockCriteriaInterfaceFactory->create();
        $searchCriteria->addFilter('products', 'product_id', ['in' => $productIds], 'public');
        $searchCriteria->addFilter('website', 'website_id', ['in' => [0, $websiteId]], 'public');

        $list = [];
        foreach($this->stockRepository->getList($searchCriteria)->getItems() as $item) { /** @var StockItemInterface $item */
            if ($item->getWebsiteId() === $websiteId || !array_key_exists($item->getProductId(), $list)) {
                $list[$item->getProductId()] = $item;
            }
        }

        return $list;
    }
}