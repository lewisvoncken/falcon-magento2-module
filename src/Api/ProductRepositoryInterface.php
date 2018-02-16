<?php

namespace Deity\MagentoApi\Api;

interface ProductRepositoryInterface extends \Magento\Catalog\Api\ProductRepositoryInterface
{
    /**
     * Get product list
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param bool $includeSubcategories
     * @param mixed $withAttributeFilters
     * @return \Deity\MagentoApi\Api\Data\ProductSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria, $includeSubcategories = false, $withAttributeFilters = []);
}
