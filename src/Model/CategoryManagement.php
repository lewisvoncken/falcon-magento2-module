<?php

namespace Hatimeria\Reagento\Model;

use Hatimeria\Reagento\Api\CategoryManagementInterface;
use Hatimeria\Reagento\Model\Category\Tree;

class CategoryManagement extends \Magento\Catalog\Model\CategoryManagement implements CategoryManagementInterface
{
    /** @var Tree */
    protected $hatimeriaCategoryTree;

    public function __construct(\Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
                                \Magento\Catalog\Model\Category\Tree $categoryTree,
                                \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoriesFactory,
                                Tree $hatimeriaCategoryTree
    ) {
        parent::__construct($categoryRepository, $categoryTree, $categoriesFactory);
        $this->hatimeriaCategoryTree = $hatimeriaCategoryTree;
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
        $result = $this->hatimeriaCategoryTree->getTree($this->hatimeriaCategoryTree->getRootNode($category), $depth);
        return $result;
    }
}