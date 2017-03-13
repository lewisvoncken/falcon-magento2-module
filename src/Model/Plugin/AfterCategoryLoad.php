<?php

namespace Hatimeria\Reagento\Model\Plugin;

use Hatimeria\Reagento\Helper\Category as HatimeriaCategoryHelper;
use Magento\Catalog\Model\Category;

class AfterCategoryLoad
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
     * @param Category $category
     * @return Category
     */
    public function afterLoad(Category $category)
    {
        $this->categoryHelper->addImageAttribute($category);
        $this->categoryHelper->addBreadcrumbs($category);

        return $category;
    }
}