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
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null
    ) {
        $this->hatimeriaCategoryHelper = $hatimeriaCategoryHelper;
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
        $this->addAttributeToSelect('url_key');
        $this->addAttributeToSelect('image');

        parent::load($printQuery, $logQuery);

        foreach ($this->_items as $category) {
            $this->hatimeriaCategoryHelper->addImageAttribute($category);
            $this->hatimeriaCategoryHelper->addBreadcrumbsData($category, $this);
        }

        return $this;
    }

    public function getItems()
    {
        if (! $this->isLoaded()) {
            $this->load();
        }

        return $this->_items;
    }


    /**
     * Retrieve item by id
     *
     * @param   mixed $idValue
     * @return  \Magento\Framework\DataObject
     */
    public function getItemById($idValue)
    {
        if (! $this->isLoaded()) {
            $this->load();
        }

        if (isset($this->_items[$idValue])) {
            return $this->_items[$idValue];
        }
        return null;
    }

}
