<?php

namespace Hatimeria\Reagento\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const RESPONSE_TAGS_REGISTRY = 'response_tags';
    /**
     * Default response tag sent in X-Cache-Tags header in REST
     */
    const defaultResponseTag = 'ApiMagento';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface 
     */
    protected $storeManager;
    /**
     * @var \Magento\Framework\App\Cache\Tag\Resolver\Proxy
     */
    protected $tagResolver;
    /**
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Cache\Tag\Resolver\Proxy $registry
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Cache\Tag\Resolver\Proxy $tagResolver
    ) {
        $this->registry     = $registry;
        $this->storeManager = $storeManager;
        $this->tagResolver  = $tagResolver;

        parent::__construct($context);
    }

    public function getAppLogoImg()
    {
        return $this->getConfigValue('app_logo_img');
    }

    public function getAppHomeUrl()
    {
        return $this->getConfigValue('app_home_url');
    }

    /**
     * Retrieve node server url from magento config or default base url
     *
     * @return string
     */
    public function getNodeServerUrl()
    {
        $url = $this->scopeConfig->getValue("reagento/general/node_server", ScopeInterface::SCOPE_STORE);

        if (empty($url)) {
            $url = $this->storeManager->getStore(0)->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK);
        }

        return $url; 
    }

    /**
     * Check configuration if node cache clear is enabled
     *
     * @return bool
     */
    public function isClearCacheEnabled()
    {
        return $this->scopeConfig->isSetFlag("reagento/general/clear_cache", ScopeInterface::SCOPE_STORE);
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
