<?php

namespace Deity\MagentoApi\Plugin\Catalog\Model;

use Deity\MagentoApi\Helper\Breadcrumb;
use Deity\MagentoApi\Helper\Category as CategoryHelper;
use Magento\Catalog\Model\Category as MagentoCategory;

class Category
{
    /** @var CategoryHelper */
    protected $categoryHelper;

    /** @var Breadcrumb */
    protected $breadcrumbHelper;

    /**
     * @param CategoryHelper $categoryHelper
     * @param Breadcrumb $breadcrumbHelper
     */
    public function __construct(CategoryHelper $categoryHelper, Breadcrumb $breadcrumbHelper)
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