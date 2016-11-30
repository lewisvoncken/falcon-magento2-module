<?php

namespace Hatimeria\Reagento\Model;

use Hatimeria\Reagento\Api\CategoryBreadcrumbsInterface;

class CategoryBreadcrumbs implements CategoryBreadcrumbsInterface
{
    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
    ) {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getList($categoryId)
    {
        /** @var \Magento\Catalog\Model\Category */
        $category = $this->categoryRepository->get($categoryId);

        $pathInStore = $category->getPathInStore();
        $pathIds = array_reverse(explode(',', $pathInStore));

        /** @var \Magento\Catalog\Model\Category[] */
        $categories = $category->getParentCategories();

        $result = [];

        foreach ($pathIds as $categoryId) {
            if (!isset($categories[$categoryId])) {
                continue;
            }
            $category = $categories[$categoryId];

            $result[] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'url_key' => $category->getUrlKey()
            ];
        }

        return $result;
    }
}
