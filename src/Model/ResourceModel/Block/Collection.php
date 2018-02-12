<?php

namespace Deity\MagentoApi\Model\ResourceModel\Block;

class Collection extends \Magento\Cms\Model\ResourceModel\Block\Collection
{
    protected $_eventPrefix = 'cms_block_collection';
    protected $_eventObject = 'collection';
}