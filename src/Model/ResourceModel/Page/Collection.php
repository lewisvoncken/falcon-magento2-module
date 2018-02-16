<?php

namespace Deity\MagentoApi\Model\ResourceModel\Page;

class Collection extends \Magento\Cms\Model\ResourceModel\Page\Collection
{
    protected $_eventPrefix = 'cms_page_collection';
    protected $_eventObject = 'collection';
}