<?php

namespace Hatimeria\Reagento\Plugin\Catalog\Model;

use Hatimeria\Reagento\Helper\Category as HatimeriaCategoryHelper;
use Magento\Catalog\Model\Category as MagentoCategory;

class Category
{
    /** @var HatimeriaCategoryHelper */
    private $categoryHelper;

    /**
     * @param HatimeriaCategoryHelper $categoryHelper
     */
    public function __construct(HatimeriaCategoryHelper $categoryHelper)
    {
        $this->categoryHelper = $categoryHelper;
    }

    /**
     * @param MagentoCategory $category
     * @return MagentoCategory
     */
    public function afterLoad(MagentoCategory $category)
    {
        $this->categoryHelper->addImageAttribute($category);
        $this->categoryHelper->addBreadcrumbsData($category);

        return $category;
    }
}