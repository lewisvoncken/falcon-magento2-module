<?php

namespace Hatimeria\Reagento\Plugin\Catalog\Model;

use Hatimeria\Reagento\Helper\Breadcrumb;
use Hatimeria\Reagento\Helper\Category as HatimeriaCategoryHelper;
use Magento\Catalog\Model\Category as MagentoCategory;

class Category
{
    /** @var HatimeriaCategoryHelper */
    protected $categoryHelper;

    /** @var Breadcrumb */
    protected $breadcrumbHelper;

    /**
     * @param HatimeriaCategoryHelper $categoryHelper
     * @param Breadcrumb $breadcrumbHelper
     */
    public function __construct(HatimeriaCategoryHelper $categoryHelper, Breadcrumb $breadcrumbHelper)
    {
        $this->categoryHelper = $categoryHelper;
        $this->breadcrumbHelper = $breadcrumbHelper;
    }

    /**
     * @param MagentoCategory $category
     * @return MagentoCategory
     * @throws \Exception
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterLoad(MagentoCategory $category)
    {
        $this->categoryHelper->addImageAttribute($category);
        $this->breadcrumbHelper->addCategoryBreadcrumbs($category);

        return $category;
    }
}