<?php

namespace Hatimeria\Reagento\Model;

use Hatimeria\Reagento\Api\Data\CategorySearchResultsInterface;
use Hatimeria\Reagento\Api\HomeCategoriesInterface;

class CategoryRepository extends \Magento\Catalog\Model\CategoryRepository implements HomeCategoriesInterface
{
    /** @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory */
    private $categoryCollection;

    /** @var \Hatimeria\Reagento\Api\Data\CategorySearchResultsInterfaceFactory */
    private $searchResultsInterfaceFactory;

    public function __construct(
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Category $categoryResource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
        \Hatimeria\Reagento\Api\Data\CategorySearchResultsInterfaceFactory $searchResultsInterfaceFactory
    ){
        parent::__construct($categoryFactory, $categoryResource, $storeManager);
        $this->categoryCollection = $categoryCollection;
        $this->searchResultsInterfaceFactory = $searchResultsInterfaceFactory;
    }

    /**
     * @return \Hatimeria\Reagento\Api\Data\CategorySearchResultsInterface
     */
    public function getHomepageList()
    {
        /** @var CategorySearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsInterfaceFactory->create();

        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $collection */
        $collection = $this->categoryCollection->create();

        $collection->addAttributeToFilter('is_on_homepage', '1')
            ->addFieldToSelect('name')
            ->addFieldToSelect('image')
            ->addFieldToSelect('url_key')
            ->addFieldToSelect('url_path')
            ->addFieldToSelect('include_in_menu')
            ->setOrder('entity_id', 'desc')
            ->setPageSize(6);

        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}