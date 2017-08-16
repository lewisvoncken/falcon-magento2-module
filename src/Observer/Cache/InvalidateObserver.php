<?php

namespace Hatimeria\Reagento\Observer\Cache;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Hatimeria\Reagento\Model\NodeServer;
use Hatimeria\Reagento\Helper\Data as ReagentoHelper;

class InvalidateObserver implements ObserverInterface
{
    /**
     * @var \Hatimeria\Reagento\Model\NodeServer
     */
    protected $nodeServer;
    /**
     * @var \Magento\Framework\App\Cache\Tag\Resolver\Proxy
     */
    protected $tagResolver;
    /**
     * @var ReagentoHelper
     */
    protected $reagentoHelper;

    /**
     *  @param \Hatimeria\Reagento\Model\NodeServer $nodeServer
     *  @param \Magento\Framework\App\Cache\Tag\Resolver\Proxy $tagResolver
     *  @param ReagentoHelper $reagentoHelper
     */
    public function __construct(
        \Hatimeria\Reagento\Model\NodeServer $nodeServer,
        \Magento\Framework\App\Cache\Tag\Resolver\Proxy $tagResolver,
        ReagentoHelper $reagentoHelper
    ) {
        $this->nodeServer     = $nodeServer;
        $this->tagResolver    = $tagResolver;
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
        $object = $observer->getObject();
        if (!$object) {

            return;
        }
        $tags = $this->tagResolver->getTags($object);
        if (empty($tags)) {

            return;
        }

        // as the coverage of tags for api requests is very small
        // for now we will keep removing whole magento cache related data
        // on tag invalidation event
        $this->nodeServer->sendInvalidate([ReagentoHelper::defaultResponseTag]);
    }

}


