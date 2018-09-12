<?php

namespace Deity\MagentoApi\Model;

use Deity\MagentoApi\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\Data\ProductSearchResultsInterfaceFactory;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper;
use Magento\Catalog\Helper\Product\ProductList;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\Product\Gallery\MimeTypeExtensionMap;
use Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks;
use Magento\Catalog\Model\Product\LinkTypeProvider;
use Magento\Catalog\Model\Product\Option\Converter;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as CatalogResourceAttribute;
use Magento\Catalog\Model\ResourceModel\Product as ResourceProduct;
use Magento\Catalog\Model\ResourceModel\Category as ResourceCategory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\CatalogInventory\Helper\Stock as StockHelper;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Framework\Api\Data\ImageContentInterfaceFactory;
use Magento\Framework\Api\ImageContentValidatorInterface;
use Magento\Framework\Api\ImageProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Filesystem;
use Magento\Store\Model\StoreManagerInterface;

class ProductRepository extends \Magento\Catalog\Model\ProductRepository implements ProductRepositoryInterface
{
    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var ResourceCategory
     */
    private $categoryResource;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var ProductList
     */
    private $productListHelper;

    /**
     * @var StockHelper
     */
    private $stockHelper;

    /**
     * @var string[] List of filters that are not regular attributes
     */
    private $specialFilters = ['in_category', 'in_stock'];

    /**
     * ProductRepository constructor.
     * @param ProductFactory $productFactory
     * @param Helper $initializationHelper
     * @param ProductSearchResultsInterfaceFactory $searchResultsFactory
     * @param ProductCollectionFactory $collectionFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ProductAttributeRepositoryInterface $attributeRepository
     * @param ResourceProduct $resourceModel
     * @param ProductLinks $linkInitializer
     * @param LinkTypeProvider $linkTypeProvider
     * @param StoreManagerInterface $storeManager
     * @param FilterBuilder $filterBuilder
     * @param ProductAttributeRepositoryInterface $metadataServiceInterface
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param Converter $optionConverter
     * @param Filesystem $fileSystem
     * @param ImageContentValidatorInterface $contentValidator
     * @param ImageContentInterfaceFactory $contentFactory
     * @param MimeTypeExtensionMap $mimeTypeExtensionMap
     * @param ImageProcessorInterface $imageProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CategoryFactory $categoryFactory
     * @param ResourceCategory $categoryResource
     * @param ResourceConnection $resource
     * @param Filter $filter
     * @param ProductList $productListHelper
     * @param StockHelper $stockHelper
     */
    public function __construct(
        ProductFactory $productFactory,
        Helper $initializationHelper,
        ProductSearchResultsInterfaceFactory $searchResultsFactory,
        ProductCollectionFactory $collectionFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductAttributeRepositoryInterface $attributeRepository,
        ResourceProduct $resourceModel,
        ProductLinks $linkInitializer,
        LinkTypeProvider $linkTypeProvider,
        StoreManagerInterface $storeManager,
        FilterBuilder $filterBuilder,
        ProductAttributeRepositoryInterface $metadataServiceInterface,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        Converter $optionConverter,
        Filesystem $fileSystem,
        ImageContentValidatorInterface $contentValidator,
        ImageContentInterfaceFactory $contentFactory,
        MimeTypeExtensionMap $mimeTypeExtensionMap,
        ImageProcessorInterface $imageProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CategoryFactory $categoryFactory,
        ResourceCategory $categoryResource,
        ResourceConnection $resource,
        Filter $filter,
        ProductList $productListHelper,
        StockHelper $stockHelper
    ) {
        parent::__construct(
            $productFactory,
            $initializationHelper,
            $searchResultsFactory,
            $collectionFactory,
            $searchCriteriaBuilder,
            $attributeRepository,
            $resourceModel,
            $linkInitializer,
            $linkTypeProvider,
            $storeManager,
            $filterBuilder,
            $metadataServiceInterface,
            $extensibleDataObjectConverter,
            $optionConverter,
            $fileSystem,
            $contentValidator,
            $contentFactory,
            $mimeTypeExtensionMap,
            $imageProcessor,
            $extensionAttributesJoinProcessor
        );
        $this->categoryFactory = $categoryFactory;
        $this->categoryResource = $categoryResource;
        $this->resource = $resource;
        $this->connection = $this->resource->getConnection();
        $this->filter = $filter;
        $this->productListHelper = $productListHelper;
        $this->stockHelper = $stockHelper;
    }

    /**
     * Get IDs of products in given categories.
     * As this method uses index table it is already filtered by status and visibility
     *
     * @param $categoryIDs
     * @return array
     */
    protected function getAllProductIds($categoryIDs)
    {
        $allProductIdsSql = $this->resource
            ->getConnection()
            ->select()
            ->from(
                $this->resource->getTableName('catalog_category_product_index'),
                ['product_id' => new \Zend_Db_Expr('DISTINCT product_id')]
            )
            ->where('store_id = ?', $this->storeManager->getStore()->getId())
            ->where('category_id IN (?)', $categoryIDs);

        return $this->resource->getConnection()->fetchCol($allProductIdsSql);
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $includeSubcategories = false, $withAttributeFilters = [])
    {
        list ($categoryIDs, $subcategories) = $this->getCategoryIdFromSearchCriteria($searchCriteria, $includeSubcategories);

        /** @var ProductCollection $collection */
        $collection = $this->collectionFactory->create();
        $this->extensionAttributesJoinProcessor->process($collection);

        foreach ($this->metadataService->getList($this->searchCriteriaBuilder->create())->getItems() as $metadata) {
            $collection->addAttributeToSelect($metadata->getAttributeCode());
        }
        $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
        $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        $this->setListPosition($collection, $searchCriteria, $categoryIDs);

        list($attributeFilters, $attributes) = $this->filter->getAttributeFilters($withAttributeFilters, $categoryIDs, $includeSubcategories);
        $this->processSearchCriteria(
            $collection,
            $searchCriteria,
            $includeSubcategories,
            ['categoryIDs' => $categoryIDs, 'subcategoryFilter' => $subcategories],
            $attributes
        );

        /** @var \Deity\MagentoApi\Api\SearchResults $searchResult */
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        if ($this->filter->isAvailabilityEnabled()) {
            /** @var int[] $allProductIds get all ids available in this category */
            $allProductIds = $this->getAllProductIds($categoryIDs);
            $productFilterOptionValues = $this->filter->getFiltersOptions($allProductIds, $withAttributeFilters, $categoryIDs);
            $attributeFilters = $this->filter->setFiltersAvailability($attributeFilters, $productFilterOptionValues, $searchCriteria, $allProductIds);
        }
        $searchResult->setFilters($attributeFilters);

        return $searchResult;
    }

    /**
     * Set ordering search result
     *
     * @param ProductCollection $collection
     * @param SearchCriteriaInterface $searchCriteria
     * @param int[] $categoryIDs
     */
    protected function setListPosition(ProductCollection $collection, SearchCriteriaInterface $searchCriteria, $categoryIDs)
    {
        $sortOrders = (array)$searchCriteria->getSortOrders();

        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $collection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
            );
        }

        if (!empty($categoryIDs) && empty($sortOrders)) {
            // info: 3 statements below is an attempt to get products for provided categories taking into account
            // category level and product position for specific category
            $categoryLevelSelect = $this->connection
                ->select()
                ->from(['category' => $this->connection->getTableName('catalog_category_entity')], ['entity_id', 'level'])
                ->where('category.entity_id in (?)', $categoryIDs)
                ->order('category.level ASC');

            $categoryPositionSelect = $this->connection
                ->select()
                ->from(['cp' => $this->connection->getTableName('catalog_category_product')], ['category_id', 'product_id', 'position'])
                ->join($categoryLevelSelect, 'cp.category_id = t.entity_id')
                ->group('cp.product_id');

            $collection->getSelect()
                ->join($categoryPositionSelect, 'product_id = e.entity_id', 'position')
                ->order('t.position ' . SortOrder::SORT_ASC);
        }
    }

    /**
     * Process search criteria data (add filters and set page and page size)
     *
     * @param ProductCollection $collection
     * @param SearchCriteriaInterface $searchCriteria
     * @param bool $includeSubcategories
     * @param array $categories
     * @param CatalogResourceAttribute[] $attributes
     */
    protected function processSearchCriteria(ProductCollection $collection, SearchCriteriaInterface $searchCriteria, $includeSubcategories, $categories, $attributes)
    {

        $categoryIDs = array_key_exists('subcategoryFilter', $categories) && !empty($categories['subcategoryFilter']) ? $categories['subcategoryFilter'] : $categories['categoryIDs'];
        //Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) { /** @var FilterGroup $group */
            if($includeSubcategories) {
                // if including products for subcategories - modify category filter group
                foreach ($group->getFilters() as $filter) {
                    /** @var \Magento\Framework\Api\Filter $filter */
                    if ($filter->getField() === 'category_id') {
                        $filter->setConditionType('in');
                        $filter->setValue($categoryIDs);
                    } elseif (in_array($filter->getField(), array_keys($attributes))) {
                        /** @var CatalogResourceAttribute $attribute */
                        $attribute = $attributes[$filter->getField()];
                        if ('multiselect' == $attribute->getFrontendInput()) {
                            $filter->setConditionType('finset');
                        }
                    }
                }
            }

            $this->addFilterGroupToCollection($group, $collection);
        }

        if (!$searchCriteria->getPageSize()) {
            $searchCriteria->setPageSize($this->getDefaultPageSize());
        }
        $collection->addPriceData(GroupInterface::NOT_LOGGED_IN_ID);
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param bool $includeSubcategories
     * @return array
     */
    protected function getCategoryIdFromSearchCriteria(SearchCriteriaInterface $searchCriteria, $includeSubcategories = false)
    {
        $categoryIDs = [];
        $subcategoryIds = [];

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            /** @var \Magento\Framework\Api\Search\FilterGroup $filterGroup */
            foreach ($filterGroup->getFilters() as $filter) {
                /** @var \Magento\Framework\Api\Filter $filter */
                if ($filter->getField() === 'category_id' && $filter->getConditionType() === 'eq') {
                    $categoryIDs[] = $filter->getValue();
                }
                if ($filter->getField() === 'in_category' && $filter->getConditionType() === 'eq') {
                    $subcategoryIds[] = $filter->getValue();
                }
            }
        }

        if (!empty($categoryIDs) && $includeSubcategories) {
            /** @var \Magento\Catalog\Model\Category $categoryEntity */
            // not loading category as the overhaul on CategoryRepositoryINterface::get is too big,
            // we just need store and path to fetch child category ids
            $categoryEntity = $this->categoryFactory->create();
            $categoryEntity->setStoreId($this->storeManager->getStore()->getId());
            $categoryEntity->setPath($this->getCategoryPath($categoryIDs[0]));
            $categoryIDs = array_merge(
                [$categoryIDs[0]],
                $this->categoryResource->getChildren($categoryEntity, true)
            );
        }

        return [$categoryIDs, $subcategoryIds];
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param ProductCollection $collection
     * @return void
     */
    protected function addFilterGroupToCollection(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        ProductCollection $collection
    ) {
        $fields = [];
        $categoryFilter = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $conditionType = $filter->getConditionType() ? $filter->getConditionType() : 'eq';

            if ($filter->getField() == 'category_id') {
                $categoryFilter[$conditionType][] = $filter->getValue();
                continue;
            }
            if ($filter->getField() === 'in_stock' && (int)$filter->getValue() === 1) {
                $this->stockHelper->addInStockFilterToCollection($collection);
            }

            if (!in_array($filter->getField(), $this->specialFilters)) {
                $fields[] = ['attribute' => $filter->getField(), $conditionType => $filter->getValue()];
            }
        }

        if ($categoryFilter) {
            $collection->addCategoriesFilter($categoryFilter);
        }

        if ($fields) {
            $collection->addFieldToFilter($fields);
        }
    }

    /**
     * Get category path base on category id directly from database
     *
     * @param int $categoryId
     * @return string
     */
    protected function getCategoryPath($categoryId)
    {
        $select = $this->connection->select()
                        ->from($this->connection->getTableName('catalog_category_entity'), 'path')
                        ->where('entity_id = ?', $categoryId);

        $path = $this->connection->fetchCol($select);

        return $path[0];
    }

    /**
     * @return string
     */
    protected function getDefaultPageSize()
    {
        return $this->productListHelper->getDefaultLimitPerPageValue(ProductList::VIEW_MODE_GRID);
    }
}