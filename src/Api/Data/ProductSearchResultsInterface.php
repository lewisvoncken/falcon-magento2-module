<?php

namespace Deity\MagentoApi\Api\Data;

interface ProductSearchResultsInterface extends \Magento\Catalog\Api\Data\ProductSearchResultsInterface
{
    /**
     * Get filters
     * @return \Deity\MagentoApi\Api\Data\FilterInterface[]
     */
    public function getFilters();

    /**
     * Set filters
     * @param \Deity\MagentoApi\Api\Data\FilterInterface[] $items
     * @return $this
     */
    public function setFilters($items);
}