<?php
namespace Deity\MagentoApi\Api\Catalog;

interface AttributeManagementInterface
{
    /**
     * @return \Deity\MagentoApi\Api\Data\FilterInterface[]
     */
    public function getCategoryFilters();
}