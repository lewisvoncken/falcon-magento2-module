<?php

namespace Hatimeria\Reagento\Model;

use Hatimeria\Reagento\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Gallery\MimeTypeExtensionMap;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Api\Data\ImageContentInterfaceFactory;
use Magento\Framework\Api\ImageContentValidatorInterface;
use Magento\Framework\Api\ImageProcessorInterface;

class ProductRepository extends \Magento\Catalog\Model\ProductRepository implements ProductRepositoryInterface
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    private $categoryFactory;

    public function __construct(
        ProductFactory $productFactory,
        \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $initializationHelper,
        \Magento\Catalog\Api\Data\ProductSearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository,
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
        \Magento\Catalog\Model\CategoryFactory $categoryFactory
    )
    {
        parent::__construct($productFactory, $initializationHelper, $searchResultsFactory, $collectionFactory, $searchCriteriaBuilder, $attributeRepository, $resourceModel, $linkInitializer, $linkTypeProvider, $storeManager, $filterBuilder, $metadataServiceInterface, $extensibleDataObjectConverter, $optionConverter, $fileSystem, $contentValidator, $contentFactory, $mimeTypeExtensionMap, $imageProcessor, $extensionAttributesJoinProcessor);
        $this->eavConfig = $eavConfig;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria, $withAttributeFilters = [])
    {
        /** @var \Hatimeria\Reagento\Api\SearchResults $list */
        $list = parent::getList($searchCriteria);

        if ($withAttributeFilters && is_string($withAttributeFilters)) {
            $withAttributeFilters = [$withAttributeFilters];
        }

        $categoryID = $this->getCategoryIdFromSearchCriteria($searchCriteria);

        if (!empty($withAttributeFilters) && $categoryID !== null) {
            $list->setFilters($this->getAttributeFilters($withAttributeFilters, $categoryID));
        }

        if ($categoryID) {
            $this->applyCategorySorting($list, $categoryID);
        }

        return $list;
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return null|int
     */
    protected function getCategoryIdFromSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            /** @var \Magento\Framework\Api\Search\FilterGroup $filterGroup */
            foreach ($filterGroup->getFilters() as $filter) {
                /** @var \Magento\Framework\Api\Filter $filter */
                if ($filter->getField() === 'category_id' && $filter->getConditionType() === 'eq') {
                    return (int)$filter->getValue();
                }
            }
        }

        return null;
    }

    /**
     * @param array $attributeFilters
     * @param int $categoryID
     * @return array
     */
    protected function getAttributeFilters($attributeFilters, $categoryID)
    {
        // TODO - check available options for products in provided $categoryID
        $result = [];
        foreach ($attributeFilters as $attributeFilter) {
            /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
            $attribute = $this->eavConfig->getAttribute('catalog_product', $attributeFilter);
            $attributeResult = [
                'label' => $attribute->getStoreLabel(),
                'code' => $attribute->getAttributeCode(),
                'options' => [],
            ];

            foreach ($attribute->getOptions() as $option) {
                if (!$option->getValue()) {
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

    /**
     * @param \Hatimeria\Reagento\Api\SearchResults $result
     * @param int $categoryID
     */
    protected function applyCategorySorting($result, $categoryID)
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = $this->categoryFactory->create()->load($categoryID);
        if(!$category) {
            return;
        }
        $position = $category->getProductsPosition();

        /** @var \Magento\Catalog\Model\Product[] $items */
        $items = $result->getItems();

        usort($items, function($a, $b) use ($position) {
            /** @var \Magento\Catalog\Model\Product $a */
            /** @var \Magento\Catalog\Model\Product $b */
            return ($position[$a->getId()] < $position[$b->getId()]) ? -1 : 1;
        });

        $result->setItems($items);
    }
}