<?php

namespace Hatimeria\Reagento\Observer\Cache;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Hatimeria\Reagento\Model\NodeServer;
use Hatimeria\Reagento\Helper\Data as ReagentoHelper;

class FlushAllObserver implements ObserverInterface
{
    /**
     * @var \Hatimeria\Reagento\Model\NodeServer
     */
    protected $nodeServer;
    /**
     * @var ReagentoHelper
     */
    protected $reagentoHelper;

    /**
     *  @param \Hatimeria\Reagento\Model\NodeServer $nodeServer
     *  @param ReagentoHelper $reagentoHelper
     */
    public function __construct(
        \Hatimeria\Reagento\Model\NodeServer $nodeServer,
        ReagentoHelper $reagentoHelper
    ) {
        $this->nodeServer     = $nodeServer;
        $this->reagentoHelper = $reagentoHelper;
    }

    /**
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        // check if node cache clear is enabled
        if (!$this->reagentoHelper->isClearCacheEnabled()) {

            return;
        }
        // by flush all we mean to remove all magento related caches, not whole node cache
        // hence usage of default response tag
        $this->nodeServer->sendInvalidate([ReagentoHelper::defaultResponseTag]);
    }

}

