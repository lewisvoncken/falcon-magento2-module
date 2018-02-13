<?php

namespace Deity\MagentoApi\Model;

use Deity\MagentoApi\Api\Data\CategoryTreeInterface;

class Category extends \Magento\Catalog\Model\Category implements CategoryTreeInterface
{
    /**
     * @return string
     */
    public function getUrlPath()
    {
        return $this->getData('url_path');
    }

    /**
     * @param string $urlPath
     * @return $this
     */
    public function setUrlPath($urlPath)
    {
        $this->setData('url_path', $urlPath);
        return $this;
    }
}