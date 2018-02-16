<?php

namespace Deity\MagentoApi\Model;

use Deity\MagentoApi\Api\Data\CategorySearchResultsInterface;
use Deity\MagentoApi\Api\HomeCategoriesInterface;

class CategoryRepository extends \Magento\Catalog\Model\CategoryRepository implements HomeCategoriesInterface
{
    /** @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory */
    private $categoryCollection;

    /** @var \Deity\MagentoApi\Api\Data\CategorySearchResultsInterfaceFactory */
    private $searchResultsInterfaceFactory;
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventDispatcher;

    public function __construct(
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Category $categoryResource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
        \Magento\Framework\Event\ManagerInterface $eventDispatcher,
        \Deity\MagentoApi\Api\Data\CategorySearchResultsInterfaceFactory $searchResultsInterfaceFactory
    ){
        parent::__construct($categoryFactory, $categoryResource, $storeManager);

        $this->categoryCollection            = $categoryCollection;
        $this->searchResultsInterfaceFactory = $searchResultsInterfaceFactory;
        $this->_eventDispatcher              = $eventDispatcher;
    }

    /**
     * @param mixed $searchCriteria
     * @return \Deity\MagentoApi\Api\Data\CategorySearchResultsInterface
     */
    public function getHomepageList($searchCriteria = [])
    {
        $pageSize = array_key_exists('pageSize', $searchCriteria) ? $searchCriteria['pageSize'] : 6;
        $page = array_key_exists('currentPage', $searchCriteria) ? $searchCriteria['currentPage'] : 1;
        /** @var CategorySearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsInterfaceFactory->create();

        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $collection */
        $collection = $this->categoryCollection->create();

        $collection->addAttributeToFilter('is_on_homepage', '1')
            ->addAttributeToFilter('is_active', 1)
            ->addFieldToSelect('name')
            ->addFieldToSelect('image')
            ->addFieldToSelect('url_key')
            ->addFieldToSelect('url_path')
            ->addFieldToSelect('include_in_menu')
            ->setOrder('homepage_position', 'asc')
            ->setPage($page, $pageSize);

        $this->_eventDispatcher->dispatch('deity_category_homepage_list_prepare_collection',
            ['collection' => $collection]);

        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
