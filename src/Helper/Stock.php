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

        /**
         * Stock item can be defined on website level so we may need either item from current website or default.
         * Unfortunately repository does not have means to return the correct one (ie. default if website is missing).
         * To mitigate this problem we need to fetch both values and select them ourselves
         */
        $searchCriteria->addFilter('website', 'website_id', ['in' => [0, $websiteId]], 'public');

        $list = [];
        foreach($this->stockRepository->getList($searchCriteria)->getItems() as $item) { /** @var StockItemInterface $item */
            /**
             * This condition will add item for website 0 if no data is in the list. If item for current website
             * is present in the result it will override default item element. If item for default website is later
             * in the result than the one for the current website it will not be added.
             */
            if ($item->getWebsiteId() === $websiteId || !array_key_exists($item->getProductId(), $list)) {
                $list[$item->getProductId()] = $item;
            }
        }

        return $list;
    }
}