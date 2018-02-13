<?php

namespace Deity\MagentoApi\Observer\Cms;

use Deity\MagentoApi\Helper\Cms as CmsHelper;
use Magento\Cms\Model\Block;
use Magento\Cms\Model\Page;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class AfterLoadObserver implements ObserverInterface
{
    /** @var CmsHelper */
    protected $cmsHelper;

    /**
     * @param CmsHelper $cmsHelper
     */
    public function __construct(CmsHelper $cmsHelper)
    {
        $this->cmsHelper = $cmsHelper;
    }

    /**
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        /** @var Block|Page $pageOrBlock */
        $pageOrBlock = $observer->getObject();

        $this->cmsHelper->filterEntityContent($pageOrBlock);
    }
}