<?php

namespace Deity\MagentoApi\Observer\Cms;

use Magento\Cms\Model\ResourceModel\AbstractCollection;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class AfterCollectionLoadObserver extends AfterLoadObserver implements ObserverInterface
{
    /**
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        /** @var AbstractCollection $collection */
        $collection = $observer->getCollection();

        foreach ($collection as $item) {
            $this->cmsHelper->filterEntityContent($item);
        }
    }
}