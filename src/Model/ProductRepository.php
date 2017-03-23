<?php

namespace Hatimeria\Reagento\Model;

use Hatimeria\Reagento\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Catalog\Model\Product\Gallery\MimeTypeExtensionMap;
use Magento\Catalog\Model\Product\Visibility as ProductVisibility;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Api\Data\ImageContentInterfaceFactory;
use Magento\Framework\Api\ImageContentValidatorInterface;
use Magento\Framework\Api\ImageProcessorInterface;
use Magento\Framework\Api\SortOrder;

class ProductRepository extends \Magento\Catalog\Model\ProductRepository implements ProductRepositoryInterface
{
    /** @var \Magento\Eav\Model\Config */
    protected $eavConfig;

    /** @var \Magento\Catalog\Model\CategoryFactory */
    protected $categoryFactory;

    /** @var \Magento\Framework\App\ResourceConnection */
    protected $resource;

    /** @var \Magento\Catalog\Model\CategoryRepository */
    protected $categoryRepository;

    public function __construct(
        ProductFactory $productFactory,
        \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $initializationHelper,
        \Magento\Catalog\Api\Data\ProductSearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Catalog\Model\ResourceModel\Product $resourceModel,
        \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks $linkInitializer,
        \Magento\Catalog\Model\Product\LinkTypeProvider $linkTypeProvider,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $metadataServiceInterface,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        \Magento\Catalog\Model\Product\Option\Converter $optionConverter,
        \Magento\Framework\Filesystem $fileSystem,
        ImageContentValidatorInterface $contentValidator,
        ImageContentInterfaceFactory $contentFactory,
        MimeTypeExtensionMap $mimeTypeExtensionMap,
        ImageProcessorInterface $imageProcessor,
        \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\App\ResourceConnection $resource
    )
    {
        parent::__construct($productFactory, $initializationHelper, $searchResultsFactory, $collectionFactory, $searchCriteriaBuilder, $attributeRepository, $resourceModel, $linkInitializer, $linkTypeProvider, $storeManager, $filterBuilder, $metadataServiceInterface, $extensibleDataObjectConverter, $optionConverter, $fileSystem, $contentValidator, $contentFactory, $mimeTypeExtensionMap, $imageProcessor, $extensionAttributesJoinProcessor);
        $this->eavConfig = $eavConfig;
        $this->categoryFactory = $categoryFactory;
        $this->resource = $resource;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria, $includeSubcategories = false, $withAttributeFilters = [])
    {
        $categoryIDs = $this->getCategoryIdFromSearchCriteria($searchCriteria, $includeSubcategories);

        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->extensionAttributesJoinProcessor->process($collection);

        foreach ($this->metadataService->getList($this->searchCriteriaBuilder->create())->getItems() as $metadata) {
            $collection->addAttributeToSelect($metadata->getAttributeCode());
        }
        $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
        $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');

        //Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            if($includeSubcategories) {
                // if including products for subcategories - modify category filter group
                foreach ($group->getFilters() as $filter) {
                    /** @var \Magento\Framework\Api\Filter $filter */
                    if ($filter->getField() === 'category_id') {
                        $filter->setConditionType('in');
                        $filter->setValue($categoryIDs);
                    }
                }
            }

            $this->addFilterGroupToCollection($group, $collection);
        }

        $sortOrders = (array)$searchCriteria->getSortOrders();

        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $collection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
            );
        }

        if(!empty($categoryIDs) && empty($sortOrders)) {
            // info: 3 statements below is an attempt to get products for provided categories taking into account
            // category level and product position for specific category
            $categoryLevelSelect = $this->resource->getConnection()
                ->select()
                ->from('catalog_category_entity', ['entity_id', 'level'])
                ->where('entity_id in (?)', $categoryIDs)
                ->order('level ASC');

            $categoryPositionSelect = $this->resource->getConnection()
                ->select()
                ->from('catalog_category_product', ['category_id', 'product_id', 'position'])
                ->join($categoryLevelSelect, 'category_id = t.entity_id')
                ->group('product_id');

            $collection->getSelect()
                ->join($categoryPositionSelect, 'product_id = e.entity_id', 'position')
                ->order('position ' . SortOrder::SORT_ASC);
        }

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

        if (!empty($withAttributeFilters) && !empty($categoryIDs)) {
            $searchResult->setFilters($this->getAttributeFilters($withAttributeFilters, $categoryIDs));
        }

        return $searchResult;
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param bool $includeSubcategories
     * @return null|array
     */
    protected function getCategoryIdFromSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria, $includeSubcategories = false)
    {
        $categoryIDs = [];

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            /** @var \Magento\Framework\Api\Search\FilterGroup $filterGroup */
            foreach ($filterGroup->getFilters() as $filter) {
                /** @var \Magento\Framework\Api\Filter $filter */
                if ($filter->getField() === 'category_id' && $filter->getConditionType() === 'eq') {
                    $categoryIDs[] = $filter->getValue();
                }
            }
        }

        if(!empty($categoryIDs) && $includeSubcategories) {
            /** @var \Magento\Catalog\Model\Category $categoryEntity */
            $categoryEntity = $this->categoryRepository->get($categoryIDs[0]);
            if($categoryEntity) {
                return $categoryEntity->getAllChildren(true);
            }
        }

        return $categoryIDs;
    }

    /**
     * @param array $attributeFilters
     * @param int|int[] $categoryID
     * @return array
     */
    protected function getAttributeFilters($attributeFilters, $categoryID)
    {
        $connection = $this->resource->getConnection();
        $select = $connection->select()
            ->distinct()
            ->from('catalog_product_entity_int', ['value'])
            ->joinLeft('catalog_category_product', 'catalog_product_entity_int.entity_id = product_id', null)
            ->where('catalog_product_entity_int.attribute_id = :attribute_id')
            ->where('category_id in (?)', $categoryID);

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
            $attributeSelect = $connection->select()
                ->from('catalog_product_entity_int', 'entity_id')
                ->joinLeft('eav_attribute', 'catalog_product_entity_int.attribute_id = eav_attribute.attribute_id', null)
                ->where('value in (?)', $attributeValues)
                ->where('attribute_code = ?', $attributeCode);

            $select->where('catalog_product_entity_int.entity_id in ?', $attributeSelect);
        }

        $result = [];
        foreach ($attributeFilters as $attributeFilter) {
            /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
            $attribute = $this->eavConfig->getAttribute('catalog_product', $attributeFilter);
            $availableOptions = $connection->fetchCol($select, [
                'attribute_id' => (int)$attribute->getId()
            ]);

            // When there's no available options for this attribute in provided category
            if(empty($availableOptions)) {
                continue;
            }

            $attributeResult = [
                'label' => $attribute->getStoreLabel(),
                'code' => $attribute->getAttributeCode(),
                'options' => [],
            ];

            foreach ($attribute-> getOptions() as $option) {
                if (!$option->getValue() || !in_array($option->getValue(), $availableOptions) ) {
                    continue;
                }

                $attributeResult['options'][] = [
                    'label' => $option->getLabel(),
                    'value' => $option->getValue()
                ];
            }

            $result[] = $attributeResult;
        }

        return $result;
    }
}