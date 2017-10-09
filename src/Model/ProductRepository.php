<?php

namespace Hatimeria\Reagento\Model;

use Hatimeria\Reagento\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\ProductSearchResultsInterfaceFactory;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\Product\Gallery\MimeTypeExtensionMap;
use Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks;
use Magento\Catalog\Model\Product\LinkTypeProvider;
use Magento\Catalog\Model\Product\Option\Converter;
use Magento\Catalog\Model\Product\Visibility as ProductVisibility;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as CatalogResourceAttribute;
use Magento\Catalog\Model\ResourceModel\Product as ResourceProduct;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Eav\Model\Config;
use Magento\Framework\Api\Data\ImageContentInterfaceFactory;
use Magento\Framework\Api\ImageContentValidatorInterface;
use Magento\Framework\Api\ImageProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Filesystem;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class ProductRepository extends \Magento\Catalog\Model\ProductRepository implements ProductRepositoryInterface
{
    /** @var Config */
    protected $eavConfig;

    /** @var CategoryFactory */
    protected $categoryFactory;

    /** @var ResourceConnection */
    protected $resource;

    /** @var CategoryRepositoryInterface */
    protected $categoryRepository;

    /** @var AdapterInterface */
    protected $connection;

    /** @var ScopeConfigInterface */
    protected $scopeConfig;

    /** @var Select[] */
    protected $queries = [];

    /**
     * @var string [] List of filters that are not regular attributes
     */
    protected $specialFilters = ['in_category'];

    /**
     * ProductRepository constructor.
     * @param ProductFactory $productFactory
     * @param Helper $initializationHelper
     * @param ProductSearchResultsInterfaceFactory $searchResultsFactory
     * @param ProductCollectionFactory $collectionFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ProductAttributeRepositoryInterface $attributeRepository
     * @param CategoryRepositoryInterface $categoryRepository
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
     * @param Config $eavConfig
     * @param CategoryFactory $categoryFactory
     * @param ResourceConnection $resource
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ProductFactory $productFactory,
        Helper $initializationHelper,
        ProductSearchResultsInterfaceFactory $searchResultsFactory,
        ProductCollectionFactory $collectionFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductAttributeRepositoryInterface $attributeRepository,
        CategoryRepositoryInterface $categoryRepository,
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
        Config $eavConfig,
        CategoryFactory $categoryFactory,
        ResourceConnection $resource,
        ScopeConfigInterface $scopeConfig
    )
    {
        parent::__construct($productFactory, $initializationHelper, $searchResultsFactory, $collectionFactory, $searchCriteriaBuilder, $attributeRepository, $resourceModel, $linkInitializer, $linkTypeProvider, $storeManager, $filterBuilder, $metadataServiceInterface, $extensibleDataObjectConverter, $optionConverter, $fileSystem, $contentValidator, $contentFactory, $mimeTypeExtensionMap, $imageProcessor, $extensionAttributesJoinProcessor);
        $this->eavConfig = $eavConfig;
        $this->categoryFactory = $categoryFactory;
        $this->resource = $resource;
        $this->categoryRepository = $categoryRepository;
        $this->scopeConfig = $scopeConfig;
        $this->connection = $this->resource->getConnection();
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

        list($attributeFilters, $attributes) = $this->getAttributeFilters($withAttributeFilters, $categoryIDs, $includeSubcategories);

        $this->processSearchCriteria($collection, $searchCriteria, $includeSubcategories,
            ['categoryIDs' => $categoryIDs, 'subcategoryFilter' => $subcategories],
            $attributes);

        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->load();

        /** @var \Hatimeria\Reagento\Api\SearchResults $searchResult */
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        if ($withAttributeFilters && is_string($withAttributeFilters)) {
            $withAttributeFilters = [$withAttributeFilters];
        }

        if (!empty($withAttributeFilters)) {
            $attributeFilters = $this->setFiltersAvailability($collection, $searchCriteria, $attributeFilters);
            $searchResult->setFilters($attributeFilters);
        }

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
                ->where('entity_id in (?)', $categoryIDs)
                ->order('level ASC');

            $categoryPositionSelect = $this->connection
                ->select()
                ->from(['cp' => $this->connection->getTableName('catalog_category_product')], ['category_id', 'product_id', 'position'])
                ->join($categoryLevelSelect, 'cp.category_id = t.entity_id')
                ->group('product_id');

            $collection->getSelect()
                ->join($categoryPositionSelect, 'product_id = e.entity_id', 'position')
                ->order('position ' . SortOrder::SORT_ASC);
        }
    }

    /**
     * Process search criteria data
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
            $categoryEntity = $this->categoryRepository->get($categoryIDs[0]);
            if ($categoryEntity) {
                $categoryIDs = $categoryEntity->getAllChildren(true);
            }
        }

        return [$categoryIDs, $subcategoryIds];
    }

    /**
     * @param array $attributeFilters
     * @param int|int[] $categoryIDs
     * @param bool $includeSubcategories
     * @return array [ array, \Magento\Catalog\Model\ResourceModel\Eav\Attribute[] ]
     */
    protected function getAttributeFilters($attributeFilters, $categoryIDs = [], $includeSubcategories = false)
    {
        $this->prepareFilterDataQueries($categoryIDs);
        $result = [];
        $resultAttributes = [];
        foreach ($attributeFilters as $attributeFilter) {
            if ($attributeFilter === 'category_id' && $includeSubcategories && !empty($categoryIDs)) {
                $attributeResult = $this->addCategoryFilter($categoryIDs[0]);
            } else {
                /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
                $attribute = $this->eavConfig->getAttribute('catalog_product', $attributeFilter);
                $attributeResult = $this->addAttributeFilter($attribute);
            }

            if (!$attributeResult) {
                continue;
            }

            $result[] = $attributeResult;
            if (isset($attribute)) {
                $resultAttributes[$attribute->getAttributeCode()] = $attribute;
            }
        }

        return [$result, $resultAttributes];
    }

    /**
     * Create data for subcategory filter
     *
     * @param int $categoryID
     * @return array
     */
    protected function addCategoryFilter($categoryID)
    {
        $selectSubCategories = array_key_exists('selectSubCategories', $this->queries) ? $this->queries['selectSubCategories'] : null;

        $storeId = $this->storeManager->getStore()->getId();
        $showCategoryFilter = $this->scopeConfig->getValue(\Hatimeria\Reagento\Helper\Category::SHOW_CATEGORY_FILTER_PATH, ScopeInterface::SCOPE_STORE, $storeId);
        if (!$selectSubCategories || !$showCategoryFilter) {
            return null;
        }

        $connection = $this->connection;
        $attributeResult = [
            'label' => __('Categories'),
            'code' => 'in_category',
            'options' => [],
        ];
        $category = $this->categoryRepository->get($categoryID);
        $attribute = $this->eavConfig->getAttribute('catalog_category', 'name');
        $subcategories = $connection->fetchAll($selectSubCategories, [
            'parent_id' => $category->getId(),
            'attribute_id' => $attribute->getAttributeId(),
            'level' => $category->getLevel() + 1
        ]);
        foreach ($subcategories as $item) {
            $attributeResult['options'][] = [
                'label' => $item['value'],
                'value' => $item['entity_id']
            ];
        }

        return $attributeResult;
    }

    /**
     * Create data for regular attribute filter
     *
     * @param CatalogResourceAttribute $attribute
     * @return array
     */
    protected function addAttributeFilter(CatalogResourceAttribute $attribute)
    {
        $connection = $this->connection;

        $selectVarchar = $this->queries['selectVarchar'];
        $selectInt = $this->queries['selectInt'];

        if (in_array($attribute->getFrontendInput(), ['multiselect', 'text'])) {
            $availableOptions = $connection->fetchCol($selectVarchar, [
                'attribute_id' => (int)$attribute->getId()
            ]);
        } else {
            $availableOptions = $connection->fetchCol($selectInt, [
                'attribute_id' => (int)$attribute->getId()
            ]);
        }

        // When there's no available options for this attribute in provided category
        if (empty($availableOptions)) {
            return null;
        }

        $attributeResult = [
            'label' => $attribute->getStoreLabel(),
            'code' => $attribute->getAttributeCode(),
            'attribute_id' => $attribute->getId(),
            'type' => in_array($attribute->getFrontendInput(), ['multiselect', 'text']) ? 'varchar' : 'int',
            'options' => [],
        ];

        if ('text' == $attribute->getFrontendInput()) {
            foreach ($availableOptions as $availableOption) {
                $attributeResult['options'][] = [
                    'label' => $availableOption,
                    'value' => $availableOption
                ];
            }
        } else {
            foreach ($attribute->getOptions() as $option) {
                if (
                    !$option->getValue() ||
                    ('multiselect' != $attribute->getFrontendInput() && !in_array($option->getValue(), $availableOptions))
                ) {
                    continue;
                }

                $attributeResult['options'][] = [
                    'label' => $option->getLabel(),
                    'value' => $option->getValue()
                ];
            }
        }

        return $attributeResult;
    }

    /**
     * Prepare select queries to use when fetching options data
     *
     * @param int[] $categoryIDs
     */
    protected function prepareFilterDataQueries($categoryIDs = [])
    {
        $connection = $this->connection;

        // subcategories filter
        $selectSubCategories = $connection->select()
            ->distinct()
            ->from(['category' => $connection->getTableName('catalog_category_entity')], ['entity_id'])
            ->joinInner(['varchar' => $connection->getTableName('catalog_category_entity_varchar')], 'varchar.entity_id = category.entity_id', 'varchar.value')
            ->where('category.parent_id = :parent_id')
            ->where('varchar.attribute_id = :attribute_id')
            ->where('category.level = :level')
            ->order('category.position ASC');

        // dropdown attributes
        $selectInt = $this->prepareAttributeSelectQuery('int');

        // multiselect attributes
        $selectVarchar = $this->prepareAttributeSelectQuery('varchar');

        if (!empty($categoryIDs)) {
            $selectInt->where('product.category_id in (?)', $categoryIDs);
            $selectVarchar->where('product.category_id in (?)', $categoryIDs);
        }

        $extraAttributes = [
            'visibility' => [
                ProductVisibility::VISIBILITY_IN_CATALOG,
                ProductVisibility::VISIBILITY_BOTH
            ],
            'status' => [
                ProductStatus::STATUS_ENABLED
            ]
        ];

        foreach ($extraAttributes as $attributeCode => $attributeValues) {
            $attributeSelectInt = $connection->select()
                ->from('catalog_product_entity_int', 'entity_id')
                ->joinLeft('eav_attribute', 'catalog_product_entity_int.attribute_id = eav_attribute.attribute_id', null)
                ->where('value in (?)', $attributeValues)
                ->where('attribute_code = ?', $attributeCode);

            $attributeSelectVarchar = $connection->select()
                ->from('catalog_product_entity_int', 'entity_id')
                ->joinLeft('eav_attribute', 'catalog_product_entity_int.attribute_id = eav_attribute.attribute_id', null)
                ->where('value in (?)', $attributeValues)
                ->where('attribute_code = ?', $attributeCode);

            $selectInt->where('attr_value.entity_id in ?', $attributeSelectInt);
            $selectVarchar->where('attr_value.entity_id in ?', $attributeSelectVarchar);
        }

        $this->queries['selectInt'] = $selectInt;
        $this->queries['selectVarchar'] = $selectVarchar;
        $this->queries['selectSubCategories'] = $selectSubCategories;
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
     *
     *
     * @param ProductCollection $collection
     * @param SearchCriteriaInterface $searchCriteria
     * @param array $attributeFilters
     * @return mixed
     */
    protected function setFiltersAvailability(ProductCollection $collection, SearchCriteriaInterface $searchCriteria, $attributeFilters)
    {
        $collection->clear();
        $collection->setPageSize(null);
        $productIds = $this->connection->fetchCol($collection->getAllIdsSql());

        $usedFilters = [];
        $activeOptions = [];
        foreach($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach($filterGroup->getFilters() as $filter) {
                $usedFilters[] = $filter->getField();
            }
        }

        foreach($attributeFilters as $id => $filter) {
            if (!in_array($filter['code'], $usedFilters)) {
                if (array_key_exists('type', $filter)) {
                    $activeOptions = $this->getActiveOptions($productIds, $filter['type'], $filter['attribute_id']);
                } elseif($filter['code'] === 'in_category') {
                    $activeOptions = $this->getActiveCategories($productIds);
                }
                $activeOptions[$filter['code']] = $activeOptions;
            }
            foreach($filter['options'] as $optId => $data) {
                if (array_key_exists($filter['code'], $activeOptions)) {
                    $active = in_array($data['value'], $activeOptions[$filter['code']]);
                } else {
                    $active = true;
                }
                $attributeFilters[$id]['options'][$optId]['active'] = $active;
            }
        }

        return $attributeFilters;
    }

    /**
     * Build basic select for getting used attribute data
     *
     * @param string $type int,varchar
     * @return Select
     */
    protected function prepareAttributeSelectQuery($type)
    {
        return $this->connection->select()
            ->distinct()
            ->from(['attr_value' => $this->connection->getTableName('catalog_product_entity_' . $type)], ['value'])
            ->joinLeft(['product' => $this->connection->getTableName('catalog_category_product')], 'attr_value.entity_id = product.product_id', null)
            ->where('attr_value.attribute_id = :attribute_id');
    }

    /**
     * @param int[] $productIds
     * @param string $type
     * @param int $attributeId
     * @return array
     */
    protected function getActiveOptions($productIds, $type, $attributeId)
    {
        $select = $this->prepareAttributeSelectQuery($type)
            ->where('attr_value.entity_id IN (?)', $productIds)
            ->distinct(true);

        return $this->connection->fetchCol($select, [
            'attribute_id' => (int)$attributeId
        ]);
    }

    /**
     * @param int[] $productIds
     * @return array
     */
    protected function getActiveCategories($productIds)
    {
        $select = $this->connection->select()
            ->from($this->connection->getTableName('catalog_category_product'), 'category_id')
            ->where('product_id IN (?)', $productIds)
            ->distinct(true);

        return $this->connection->fetchCol($select);
    }
}