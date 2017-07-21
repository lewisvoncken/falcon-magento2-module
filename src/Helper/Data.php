<?php

namespace Hatimeria\Reagento\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const RESPONSE_TAGS_REGISTRY = 'response_tags';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    /**
     * @var \Magento\Framework\App\Cache\Tag\Resolver\Proxy
     */
    protected $tagResolver;
    /**
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Cache\Tag\Resolver\Proxy $registry
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Cache\Tag\Resolver\Proxy $tagResolver
    ) {
        $this->registry    = $registry;
        $this->tagResolver = $tagResolver;
    }

    public function getAppLogoImg()
    {
        return $this->getConfigValue('app_logo_img');
    }

    public function getAppHomeUrl()
    {
        return $this->getConfigValue('app_home_url');
    }

    private function getConfigValue($key, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue("hatimeria/reagento/$key", $scope);
    }

    /**
     * Add tags to response tag registry
     *
     * @param mixed $tags
     */
    public function addResponseTags($tags)
    {
        if (!is_array($tags)) {
            $tags = [$tags];
        }

        $currentTags = $this->getResponseTags();
        if (!is_array($currentTags)) {
            $currentTags = [];
        }

        $tags = array_merge($currentTags, $tags);

        $this->setResponseTags($tags);
    }

    /**
     * Add response tags from object to registry
     *
     * @param mixed $object
     */
    public function addResponseTagsByObject($object)
    {
        $this->addResponseTags($this->tagResolver->getTags($object));
    }

    /**
     * Set list of response tags in registry
     * 
     * @param mixed $tags
     */
    public function setResponseTags($tags)
    {
        if (null !== $tags && !is_array($tags)) {
            throw new \Exception('Not accepted argument');
        }

        $this->registry->unregister(self::RESPONSE_TAGS_REGISTRY);
        $this->registry->register(self::RESPONSE_TAGS_REGISTRY, $tags, true);
    }

    /**
     * List of collected response tags in registry
     *
     * @return array
     */
    public function getResponseTags()
    {
        return $this->registry->registry(self::RESPONSE_TAGS_REGISTRY);
    }

}
