<?php

namespace Deity\MagentoApi\Model;

use Deity\MagentoApi\Api\CategoryBreadcrumbsInterface;

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

        $result = [];

        foreach ($pathIds as $categoryId) {
            $parentCategory = $this->categoryRepository->get($categoryId);

            $result[] = [
                'id' => $parentCategory->getId(),
                'name' => $parentCategory->getName(),
                'url_path' => $parentCategory->getUrlPath(),
                'url_key' => $parentCategory->getUrlKey()
            ];
        }

        return $result;
    }
}
