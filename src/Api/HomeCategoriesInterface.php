<?php

namespace Hatimeria\Reagento\Api;

interface HomeCategoriesInterface
{
    /**
     *
     * @param mixed $searchCriteria
     * @return \Hatimeria\Reagento\Api\Data\CategorySearchResultsInterface
     */
    public function getHomepageList($searchCriteria = []);
}