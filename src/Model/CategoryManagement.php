<?php

namespace Deity\MagentoApi\Model;

use Deity\MagentoApi\Api\CategoryManagementInterface;
use Deity\MagentoApi\Model\Category\Tree;

class CategoryManagement extends \Magento\Catalog\Model\CategoryManagement implements CategoryManagementInterface
{
    /** @var Tree */
    protected $deityCategoryTree;

    public function __construct(\Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
                                \Magento\Catalog\Model\Category\Tree $categoryTree,
                                \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoriesFactory,
                                Tree $deityCategoryTree
    ) {
        parent::__construct($categoryRepository, $categoryTree, $categoriesFactory);
        $this->deityCategoryTree = $deityCategoryTree;
    }

    /**
     * {@inheritdoc}
     */
    public function getTree($rootCategoryId = null, $depth = null)
    {
        $category = null;
        if ($rootCategoryId !== null) {
            /** @var \Magento\Catalog\Model\Category $category */
            $category = $this->categoryRepository->get($rootCategoryId);
        }
        $result = $this->deityCategoryTree->getTree($this->deityCategoryTree->getRootNode($category), $depth);
        return $result;
    }
}