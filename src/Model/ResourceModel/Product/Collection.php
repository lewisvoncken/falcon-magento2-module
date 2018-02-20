<?php

namespace Deity\MagentoApi\Model\ResourceModel\Product;

class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    /**
     * @inheritdoc
     */
    protected function _afterLoad()
    {
        $this->addCategoryIds();

        return parent::_afterLoad();
    }
}
