<?php
namespace Hatimeria\Reagento\Api\Catalog;

interface AttributeManagementInterface
{
    /**
     * @return \Hatimeria\Reagento\Api\Data\FilterInterface[]
     */
    public function getCategoryFilters();
}