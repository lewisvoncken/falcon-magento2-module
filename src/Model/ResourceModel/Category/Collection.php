<?php

namespace Hatimeria\Reagento\Model\ResourceModel\Category;

use Hatimeria\Reagento\Helper\Category;
use Magento\Catalog\Model\ResourceModel\Category\Collection as MagentoCategoryCollection;
use Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection;

class Collection extends MagentoCategoryCollection
{
    /**
     * @var Category
     */
    protected $hatimeriaCategoryHelper;

    /**
     * @var AbstractCollection
     */
    protected $abstractCollection;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Eav\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Hatimeria\Reagento\Helper\Category $hatimeriaCategoryHelper,
        \Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection $abstractCollection,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null
    ) {
        $this->hatimeriaCategoryHelper = $hatimeriaCategoryHelper;
        $this->abstractCollection = $abstractCollection;
        return parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $storeManager,
            $connection
        );
    }
    /**
     * Load collection
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        $this->addAttributeToSelect('url_path');

        if ($this->_loadWithProductCount) {
            $this->addAttributeToSelect('all_children');
            $this->addAttributeToSelect('is_anchor');
        }

        $this->abstractCollection->load($printQuery, $logQuery);

        if ($this->_loadWithProductCount) {
            $this->_loadProductCount();
        }
        foreach ($this as $category) {
            $this->hatimeriaCategoryHelper->addImageAttribute($category);
            $this->hatimeriaCategoryHelper->addBreadcrumbsData($category);
        }
        return $this;
    }
}
