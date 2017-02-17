<?php

namespace Hatimeria\Reagento\Api\Data;

interface ProductSearchResultsInterface extends \Magento\Catalog\Api\Data\ProductSearchResultsInterface
{
    /**
     * Get filters
     * @return mixed
     */
    public function getFilters();

    /**
     * Set filters
     * @param mixed $items
     * @return $this
     */
    public function setFilters(array $items);
}