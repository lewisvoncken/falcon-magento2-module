<?php

namespace Hatimeria\Reagento\Observer\Catalog\Category\Collection;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * @package Hatimeria\Reagento\Observer
 */
class AfterLoadObserver implements ObserverInterface
{
    /** @var \Hatimeria\Reagento\Helper\Category */
    private $categoryHelper;

    /**
     * @param \Hatimeria\Reagento\Helper\Category $categoryHelper
     */
    public function __construct(\Hatimeria\Reagento\Helper\Category $categoryHelper)
    {
        $this->categoryHelper = $categoryHelper;
    }

    /**
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        /** @var CategoryCollection $collection */
        $collection = $observer->getEvent()->getCategoryCollection();

        foreach ($collection as $item) {
            /** @var Category $item */
            $this->categoryHelper->addImageAttribute($item);
        }
    }
}