<?php

namespace Deity\MagentoApi\Api;

interface HomeCategoriesInterface
{
    /**
     *
     * @param mixed $searchCriteria
     * @return \Deity\MagentoApi\Api\Data\CategorySearchResultsInterface
     */
    public function getHomepageList($searchCriteria = []);
}