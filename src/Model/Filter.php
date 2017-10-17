<?php

namespace Hatimeria\Reagento\Model;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Catalog\Model\Product\Visibility as ProductVisibility;

class Filter
{
    /** @var ResourceConnection */
    protected $resourceConnection;

    /** @var Config */
    protected $eavConfig;

    /** @var StoreManagerInterface */
    protected $storeManager;

    /** @var LoggerInterface */
    protected $logger;

    /** @var Select[] */
    protected $queries = [];

    /**
     * Filter constructor.
     * @param ResourceConnection $resourceConnection
     * @param Config $eavConfig
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        Config $eavConfig,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->eavConfig = $eavConfig;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * Fetch attribute value for all possible products and all filters
     *
     * @param int[] $productIds
     * @param string[] $attributes
     * @param int[] $categories
     * @return array
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
            $filtersOptions[$attribute->getAttributeCode()] = $connection->fetchPairs($select);
        }

        return $filtersOptions;
    }

    /**
     *
     *
     * @param array $attributeFilters
     * @param $productOptionValues
     * @param SearchCriteriaInterface $searchCriteria
     * @return mixed
     */
    public function setFiltersAvailability($attributeFilters, $productOptionValues, SearchCriteriaInterface $searchCriteria)
    {
        $usedFilters = [];
        $usedProducts = [];
        $init = true;
        foreach($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach($filterGroup->getFilters() as $filter) {
                if($filter->getField() === 'in_category') {
                    $products = [];
                    foreach($productOptionValues[$filter->getField()] as $product => $categories) {
                        if (in_array($filter->getValue(), $categories)) {
                            $products[] = $product;
                        }
                    }
                } elseif (array_key_exists($filter->getField(), $productOptionValues)) {
                    $products = array_keys($productOptionValues[$filter->getField()], $filter->getValue());
                } else {
                    continue;
                }

                $usedFilters[$filter->getField()] = [
                    'value' => $filter->getValue(),
                    'products' => $products
                ];
                $usedProducts = $init ? $products : array_intersect($usedProducts, $products);
            }
        }
        foreach($attributeFilters as $id => $filter) {
            $code = $filter['code'];
            if (!array_key_exists($code, $usedFilters)) {
                $
            }
        }


        return $attributeFilters;
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
}