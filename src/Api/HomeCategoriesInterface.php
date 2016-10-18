<?php

namespace Hatimeria\Reagento\Api;

interface HomeCategoriesInterface
{
    /**
     * @return \Hatimeria\Reagento\Api\Data\CategorySearchResultsInterface
     */
    public function getHomepageList();
}