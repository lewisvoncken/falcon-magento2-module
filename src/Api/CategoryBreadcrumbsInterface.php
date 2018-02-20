<?php

namespace Deity\MagentoApi\Api;

interface CategoryBreadcrumbsInterface
{
    /**
     * @param int $categoryId
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList($categoryId);
}
