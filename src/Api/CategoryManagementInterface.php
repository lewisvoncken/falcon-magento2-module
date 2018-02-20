<?php

namespace Deity\MagentoApi\Api;

interface CategoryManagementInterface
{
    /**
     * @param int|null $rootCategoryId
     * @param int|null $depth
     * @return \Deity\MagentoApi\Api\Data\CategoryTreeInterface
     */
    public function getTree($rootCategoryId = null, $depth = null);
}