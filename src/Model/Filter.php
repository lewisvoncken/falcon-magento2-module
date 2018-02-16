<?php

namespace Deity\MagentoApi\Model;

use Deity\MagentoApi\Api\Data\FilterInterface;
use Deity\MagentoApi\Api\Data\FilterInterfaceFactory;
use Deity\MagentoApi\Api\Data\FilterOptionInterface;
use Deity\MagentoApi\Api\Data\FilterOptionInterfaceFactory;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Catalog\Model\Product\Visibility as ProductVisibility;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as CatalogResourceAttribute;

/**
 * Product list filter handling class
 */
class Filter
{
    const AVAILABILITY_ENABLED = 'deity/catalog/availability_status';

    /** @var ResourceConnection */
    protected $resourceConnection;

    /** @var Config */
    protected $eavConfig;

    /** @var StoreManagerInterface */
    protected $storeManager;

    /** @var ScopeConfigInterface */
    protected $scopeConfig;

    /** @var CategoryRepositoryInterface */
    protected $categoryRepository;

    /** @var LoggerInterface */
    protected $logger;

    /** @var FilterInterfaceFactory */
    protected $filterFactory;

    /** @var FilterOptionInterfaceFactory */
    protected $filterOptionFactory;

    /** @var Select[] */
    protected $queries = [];

    /**
     * Filter constructor.
     * @param ResourceConnection $resourceConnection
     * @param Config $eavConfig
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param CategoryRepositoryInterface $categoryRepository
     * @param LoggerInterface $logger
     * @param FilterInterfaceFactory $filterFactory
     * @param FilterOptionInterfaceFactory $filterOptionFactory
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        Config $eavConfig,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        CategoryRepositoryInterface $categoryRepository,
        LoggerInterface $logger,
        FilterInterfaceFactory $filterFactory,
        FilterOptionInterfaceFactory $filterOptionFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->eavConfig = $eavConfig;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->categoryRepository = $categoryRepository;
        $this->logger = $logger;
        $this->filterFactory = $filterFactory;
        $this->filterOptionFactory = $filterOptionFactory;
    }

    /**
     * Get value of filter availability setting
     *
     * @return bool
     */
    public function isAvailabilityEnabled()
    {
        return (bool)$this->scopeConfig->getValue(
            self::AVAILABILITY_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );
    }

    /**
     * @param array $attributeFilters
     * @param int|int[] $categoryIDs
     * @param bool $includeSubcategories
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAttributeFilters($attributeFilters, $categoryIDs = [], $includeSubcategories = false)
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
     * Fetch attribute value for all possible products and all filters
     *
     * @param int[] $productIds
     * @param string[] $attributes
     * @param int[] $categories
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFiltersOptions($productIds, $attributes, $categories)
    {
        $connection = $this->resourceConnection->getConnection();
        $filtersOptions = [];
        $explodeResult = function($value) {return explode(',', $value);};

        foreach($attributes as $code) {
            if ($code === 'category_id') {
                $select = $this->getCategoryProductAssociation($productIds, $categories);
                $result = $connection->fetchPairs($select);
                $result = array_map($explodeResult, $result);
                $filtersOptions['in_category'] = $result;
                continue;
            }

            $attribute = $this->eavConfig->getAttribute(Product::ENTITY, $code);
            switch ($attribute->getBackendType()) {
                case 'varchar':
                    $select = $this->getAttributeProductValueSelect('varchar', $attribute->getId(), $productIds);
                    break;
                case 'int':
                    $select = $this->getAttributeProductValueSelect('int', $attribute->getId(), $productIds);
                    break;
                default:
                    continue;
                    break;
            }
            if (!isset($select)) {
                continue;
            }
            $filtersOptions[$attribute->getAttributeCode()] = $connection->fetchPairs($select);
        }

        return $filtersOptions;
    }

    /**
     *
     *
     * @param FilterInterface[] $attributeFilters
     * @param array $productOptionValues
     * @param SearchCriteriaInterface $searchCriteria
     * @param int[] $allProductsIds
     * @return mixed
     */
    public function setFiltersAvailability(
        $attributeFilters,
        $productOptionValues,
        SearchCriteriaInterface $searchCriteria,
        $allProductsIds
    ) {
        $optionValuesProducts = $this->convertProductOptionsValuesToOptionValuesProducts($productOptionValues);
        list ($usedFilters, $usedProducts) = $this->getUsedProducts($searchCriteria, $optionValuesProducts, $allProductsIds);

        foreach($attributeFilters as $filter) { /** @var FilterInterface $filter */
            $code = $filter->getCode();
            if (!array_key_exists($code, $optionValuesProducts)) {
                continue;
            }
            $productBasis = $usedProducts;
            if (array_key_exists($code, $usedFilters)) {
                //create a basis without currently used filter
                $productBasis = $this->getProductIdBaseWithoutFilter($allProductsIds, $usedFilters, $code);
            }

            $filter->setOptions(
                $this->setOptionAvailability(
                    $filter->getOptions(),
                    $optionValuesProducts[$code],
                    $productBasis
                )
            );
        }

        return $attributeFilters;
    }

    /**
     * Create data for subcategory filter
     *
     * @param int $categoryID
     * @return FilterInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function addCategoryFilter($categoryID)
    {
        $selectSubCategories = array_key_exists('selectSubCategories', $this->queries) ? $this->queries['selectSubCategories'] : null;

        $storeId = $this->storeManager->getStore()->getId();
        $showCategoryFilter = $this->scopeConfig->getValue(
            \Deity\MagentoApi\Helper\Category::SHOW_CATEGORY_FILTER_PATH,
            ScopeInterface::SCOPE_STORE, $storeId
        );
        if (!$selectSubCategories || !$showCategoryFilter) {
            return null;
        }

        $connection = $this->resourceConnection->getConnection();
        /** @var FilterInterface $attributeResult */
        $attributeResult = $this->filterFactory->create();
        $attributeResult->setLabel(__('Categories'));
        $attributeResult->setCode('in_category');

        $category = $this->categoryRepository->get($categoryID);
        $attribute = $this->eavConfig->getAttribute('catalog_category', 'name');
        $subcategories = $connection->fetchAll($selectSubCategories, [
            'parent_id' => $category->getId(),
            'attribute_id' => $attribute->getAttributeId(),
            'level' => $category->getLevel() + 1
        ]);

        $options = [];
        foreach ($subcategories as $item) {
            /** @var FilterOptionInterface $attributeResultOption */
            $attributeResultOption = $this->filterOptionFactory->create();
            $attributeResultOption->setLabel($item['value']);
            $attributeResultOption->setValue($item['entity_id']);
            $options[] = $attributeResultOption;
        }
        $attributeResult->setOptions($options);

        return $attributeResult;
    }

    /**
     * Create data for regular attribute filter
     *
     * @param CatalogResourceAttribute $attribute
     * @return FilterInterface
     */
    protected function addAttributeFilter(CatalogResourceAttribute $attribute)
    {
        $connection = $this->resourceConnection->getConnection();

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

        /** @var FilterInterface $attributeResult */
        $attributeResult = $this->filterFactory->create();
        $attributeResult->setLabel($attribute->getStoreLabel())
            ->setCode($attribute->getAttributeCode())
            ->setAttributeId($attribute->getId())
            ->setType(in_array($attribute->getFrontendInput(), ['multiselect', 'text']) ? 'varchar' : 'int');

        $options = [];
        if ('text' == $attribute->getFrontendInput()) {
            foreach ($availableOptions as $availableOption) {
                /** @var FilterOptionInterface $attributeResultOption */
                $attributeResultOption = $this->filterOptionFactory->create();
                $attributeResultOption->setLabel($availableOption)
                    ->setValue($availableOption);
                $options[] = $attributeResultOption;
            }
        } else {
            foreach ($attribute->getOptions() as $option) {
                if (
                    !$option->getValue() ||
                    ('multiselect' != $attribute->getFrontendInput() && !in_array($option->getValue(), $availableOptions))
                ) {
                    continue;
                }

                /** @var FilterOptionInterface $attributeResultOption */
                $attributeResultOption = $this->filterOptionFactory->create();
                $attributeResultOption->setLabel($option->getLabel())
                    ->setValue($option->getValue());
                $options[] = $attributeResultOption;
            }
        }
        $attributeResult->setOptions($options);

        return $attributeResult;
    }

    /**
     * Prepare select queries to use when fetching options data
     *
     * @param int[] $categoryIDs
     */
    protected function prepareFilterDataQueries($categoryIDs = [])
    {
        $connection = $this->resourceConnection->getConnection();

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
     * Build basic select for getting used attribute data
     *
     * @param string $type int,varchar
     * @return Select
     */
    protected function prepareAttributeSelectQuery($type)
    {
        $connection = $this->resourceConnection->getConnection();
        return $connection->select()
            ->distinct()
            ->from(['attr_value' => $connection->getTableName('catalog_product_entity_' . $type)], ['value'])
            ->joinLeft(['product' => $connection->getTableName('catalog_category_product')], 'attr_value.entity_id = product.product_id', null)
            ->where('attr_value.attribute_id = :attribute_id');
    }

    /**
     * Prepare query to select specific attribute values for products
     *
     * @param string $type
     * @param int $attributeId
     * @param int[] $products
     * @return Select
     */
    protected function getAttributeProductValueSelect($type, $attributeId, $products)
    {
        return $this->resourceConnection->getConnection()->select()
            ->from([
                'attr_value' => $this->resourceConnection->getTableName('catalog_product_entity_' . $type)],
                [
                    'entity_id' => 'attr_value_default.entity_id',
                    'value' => new \Zend_Db_Expr('IFNULL(attr_value.value, attr_value_default.value)')
                ]
            )
            ->joinLeft(
                ['attr_value_default' => $this->resourceConnection->getTableName('catalog_product_entity_varchar')],
                'attr_value_default.attribute_id = attr_value.attribute_id AND attr_value_default.entity_id = attr_value.entity_id',
                null
            )
            ->where('attr_value.attribute_id = ?', $attributeId)
            ->where('attr_value.store_id IN (?)', [0, (int)$this->storeManager->getStore()->getId()])
            ->where('attr_value.entity_id IN (?)', $products)
            ->group(['attr_value.entity_id', 'attr_value.attribute_id']);
    }

    /**
     * Prepare query to fetch all category-product association
     *
     * @param int[] $products
     * @param int[] $categories
     * @return Select
     */
    protected function getCategoryProductAssociation($products, $categories)
    {
        return $this->resourceConnection->getConnection()->select()
            ->from([
                'cat_prod' => $this->resourceConnection->getTableName('catalog_category_product_index')],
                [
                    'entity_id' => 'cat_prod.product_id',
                    'value' => new \Zend_Db_Expr("GROUP_CONCAT(DISTINCT cat_prod.category_id SEPARATOR ',')")
                ]
            )
            ->where('cat_prod.product_id IN (?)', $products)
            ->where('cat_prod.category_id IN (?)', $categories)
            ->where('cat_prod.store_id = ? ', (int)$this->storeManager->getStore()->getId())
            ->group('cat_prod.product_id');
    }

    /**
     * Find each option availability status
     *
     * @param FilterOptionInterface[] $options list of filter options
     * @param array $optionValuesProducts list of products with values
     * @param array $usedProducts list of product ids used as a basis for filter
     * @return array
     */
    protected function setOptionAvailability($options, $optionValuesProducts, $usedProducts)
    {
        foreach ($options as $option) { /** @var FilterOptionInterface $option */
            $value = $option->getValue();
            $optionProducts = isset($optionValuesProducts[$value]) ? $optionValuesProducts[$value] : [];
            $active = !empty($optionProducts) ? !empty(array_intersect($usedProducts, $optionProducts)) : false;
            $option->setActive($active);
        }

        return $options;
    }

    /**
     * @param $searchCriteria
     * @param $optionValuesProducts
     * @param $allProductsIds
     * @return array
     */
    protected function getUsedProducts($searchCriteria, $optionValuesProducts, $allProductsIds)
    {
        $usedFilters = [];
        $usedProducts = $allProductsIds;
        foreach($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach($filterGroup->getFilters() as $filter) {
                if (isset($optionValuesProducts[$filter->getField()][$filter->getValue()])) {
                    $products = $optionValuesProducts[$filter->getField()][$filter->getValue()];
                } else {
                    continue;
                }

                $usedFilters[$filter->getField()] = [
                    'value' => $filter->getValue(),
                    'products' => $products
                ];
                $usedProducts = array_intersect($usedProducts, $products);
            }
        }

        return [$usedFilters, $usedProducts];
    }

    /**
     * @param int[] $allProductIds list of all product ids
     * @param array $usedFilters list of used filters with products of that filter
     * @param string $excludeFilterCode name of the filter to exclude
     * @return array
     */
    protected function getProductIdBaseWithoutFilter($allProductIds, $usedFilters, $excludeFilterCode)
    {
        $usedProducts = $allProductIds;
        foreach($usedFilters as $code => $data) {
            if ($code !== $excludeFilterCode) {
                $usedProducts = array_intersect($usedProducts, $data['products']);
            }
        }

        return $usedProducts;
    }

    /**
     * Convert array where each product has its value of the given attribute to an array where each option has a list of available products
     *
     * @param array $productOptionValues
     * @return array
     */
    protected function convertProductOptionsValuesToOptionValuesProducts($productOptionValues)
    {
        $optionValuesProducts = [];
        foreach($productOptionValues as $code => $options) {
            foreach ($options as $productId => $optionValues) {
                if (is_array($optionValues)) {
                    foreach ($optionValues as $optId) {
                        if (!isset($optionValuesProducts[$code][$optId])) {
                            $optionValuesProducts[$code][$optId] = [];
                        }
                        $optionValuesProducts[$code][$optId][] = $productId;
                    }
                } else {
                    if (!isset($optionValuesProducts[$code][$optionValues])) {
                        $optionValuesProducts[$code][$optionValues] = [];
                    }
                    $optionValuesProducts[$code][$optionValues][] = $productId;
                }
            }
        }
        return $optionValuesProducts;
    }
}
