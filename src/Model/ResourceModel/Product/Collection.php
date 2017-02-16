<?php

namespace Hatimeria\Reagento\Model\ResourceModel\Product;

class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    /**
     * @inheritdoc
     */
    protected function _beforeLoad()
    {
        $this->_eventManager->dispatch('catalog_product_collection_load_before', [
            'collection' => $this
        ]);

        return parent::_beforeLoad();
    }

    /**
     * @inheritdoc
     */
    protected function _afterLoad()
    {
        $this->addCategoryIds();

        return parent::_afterLoad();
    }
}
