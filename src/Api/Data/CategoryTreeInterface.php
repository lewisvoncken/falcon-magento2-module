<?php

namespace Hatimeria\Reagento\Api\Data;

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
     * @return \Hatimeria\Reagento\Api\Data\CategoryTreeInterface[]
     */
    public function getChildrenData();

    /**
     * @param \Hatimeria\Reagento\Api\Data\CategoryTreeInterface[] $childrenData
     * @return $this
     */
    public function setChildrenData(array $childrenData = null);
}