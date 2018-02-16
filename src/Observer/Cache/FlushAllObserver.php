<?php

namespace Deity\MagentoApi\Observer\Cache;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Deity\MagentoApi\Model\NodeServer;
use Deity\MagentoApi\Helper\Data as DeityHelper;

class FlushAllObserver implements ObserverInterface
{
    /**
     * @var \Deity\MagentoApi\Model\NodeServer
     */
    protected $nodeServer;
    /**
     * @var DeityHelper
     */
    protected $deityHelper;

    /**
     *  @param \Deity\MagentoApi\Model\NodeServer $nodeServer
     *  @param DeityHelper $deityHelper
     */
    public function __construct(
        \Deity\MagentoApi\Model\NodeServer $nodeServer,
        DeityHelper $deityHelper
    ) {
        $this->nodeServer     = $nodeServer;
        $this->deityHelper = $deityHelper;
    }

    /**
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        // check if node cache clear is enabled
        if (!$this->deityHelper->isClearCacheEnabled()) {

            return;
        }
        // by flush all we mean to remove all magento related caches, not whole node cache
        // hence usage of default response tag
        $this->nodeServer->sendInvalidate([DeityHelper::defaultResponseTag]);
    }

}

