<?php

namespace Deity\MagentoApi\Api\Data;

interface CategoryTreeInterface extends \Magento\Catalog\Api\Data\CategoryTreeInterface
{
    /**
     * @return string
     */
    public function getUrlPath();

    /**
     * @param string
     * @return $this
     */
    public function setUrlPath($urlPath);

    /**
     * @return \Deity\MagentoApi\Api\Data\CategoryTreeInterface[]
     */
    public function getChildrenData();

    /**
     * @param \Deity\MagentoApi\Api\Data\CategoryTreeInterface[] $childrenData
     * @return $this
     */
    public function setChildrenData(array $childrenData = null);
}