<?php

namespace Deity\MagentoApi\Observer\Cache;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Deity\MagentoApi\Model\NodeServer;
use Deity\MagentoApi\Helper\Data as DeityHelper;

class InvalidateObserver implements ObserverInterface
{
    /**
     * @var \Deity\MagentoApi\Model\NodeServer
     */
    protected $nodeServer;
    /**
     * @var \Magento\Framework\App\Cache\Tag\Resolver\Proxy
     */
    protected $tagResolver;
    /**
     * @var DeityHelper
     */
    protected $deityHelper;

    /**
     *  @param \Deity\MagentoApi\Model\NodeServer $nodeServer
     *  @param \Magento\Framework\App\Cache\Tag\Resolver\Proxy $tagResolver
     *  @param DeityHelper $deityHelper
     */
    public function __construct(
        \Deity\MagentoApi\Model\NodeServer $nodeServer,
        \Magento\Framework\App\Cache\Tag\Resolver\Proxy $tagResolver,
        DeityHelper $deityHelper
    ) {
        $this->nodeServer     = $nodeServer;
        $this->tagResolver    = $tagResolver;
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
        $this->nodeServer->sendInvalidate([DeityHelper::defaultResponseTag]);
    }

}


