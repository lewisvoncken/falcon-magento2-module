<?php

namespace Hatimeria\Reagento\Api;

interface CategoryManagementInterface
{
    /**
     * @param int|null $rootCategoryId
     * @param int|null $depth
     * @return \Hatimeria\Reagento\Api\Data\CategoryTreeInterface
     */
    public function getTree($rootCategoryId = null, $depth = null);
}