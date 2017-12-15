<?php

namespace Hatimeria\Reagento\Api\Data;

interface ProductSearchResultsInterface extends \Magento\Catalog\Api\Data\ProductSearchResultsInterface
{
    /**
     * Get filters
     * @return \Hatimeria\Reagento\Api\Data\FilterInterface[]
     */
    public function getFilters();

    /**
     * Set filters
     * @param \Hatimeria\Reagento\Api\Data\FilterInterface[] $items
     * @return $this
     */
    public function setFilters($items);
}