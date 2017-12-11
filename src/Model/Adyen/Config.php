<?php

namespace Hatimeria\Reagento\Model\Adyen;

use Hatimeria\Reagento\Api\Data\AdyenConfigInterface;
use Hatimeria\Reagento\Api\Data\AdyenConfigInterfaceFactory;
use Hatimeria\Reagento\Api\HatimeriaAdyenConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Config implements HatimeriaAdyenConfigInterface
{
    const ADYEN_CONFIG_MODE = 'payment/adyen_abstract/demo_mode';
    const ADYEN_CSE_CONFIG_LIVE = 'payment/adyen_cc/cse_publickey_live';
    const ADYEN_CSE_CONFIG_TEST = 'payment/adyen_cc/cse_publickey_test';
    const ADYEN_CC_ENABLED = 'payment/adyen_cc/active';
    const ADYEN_CC_TYPES = 'payment/adyen_cc/cctypes';
    const ADYEN_CC_IMAGE = 'payment/adyen_cc/cc_image';

    /** @var StoreManagerInterface */
    protected $storeManager;

    /** @var ScopeConfigInterface */
    protected $scopeConfig;

    /** @var AdyenConfigInterfaceFactory */
    protected $adyenConfigFactory;

    /** @var UrlInterface */
    protected $urlBuilder;

    /**
     * Config constructor.
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param AdyenConfigInterfaceFactory $adyenConfigFactory
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        AdyenConfigInterfaceFactory $adyenConfigFactory,
        UrlInterface $urlBuilder
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->adyenConfigFactory = $adyenConfigFactory;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param null $storeId
     * @return AdyenConfigInterface
     */
    public function getConfig($storeId = null)
    {
        $storeId = $storeId ?: $this->storeManager->getStore($storeId)->getId();
        $testMode = $this->scopeConfig->getValue(self::ADYEN_CONFIG_MODE, ScopeInterface::SCOPE_STORE, $storeId);
        $csePath = $testMode ? self::ADYEN_CSE_CONFIG_TEST : self::ADYEN_CSE_CONFIG_LIVE;

        $configPaths = [
            self::ADYEN_CC_ENABLED,
            self::ADYEN_CC_TYPES,
            self::ADYEN_CC_IMAGE,
            $csePath
        ];

        $configs = $this->fetchConfig($configPaths, $storeId);

        $adyenConfig = $this->getAdyenConfigDataObject();
        $adyenConfig->setCcEnabled($configs[self::ADYEN_CC_ENABLED]);
        $adyenConfig->setCcAvailableCards($configs[self::ADYEN_CC_TYPES]);
        $adyenConfig->setCsePublicKey($configs[$csePath]);

        $imageFile = $configs[self::ADYEN_CC_IMAGE];
        if ($imageFile) {
            $adyenConfig->setCcImage($this->getImageUrl($imageFile));
        }

        return $adyenConfig;
    }

    /**
     * @return AdyenConfigInterface
     */
    protected function getAdyenConfigDataObject()
    {
        return $this->adyenConfigFactory->create();
    }

    /**
     * @param string[] $paths
     * @param int $storeId
     * @return array
     */
    protected function fetchConfig($paths, $storeId)
    {
        $data = [];
        foreach($paths as $path) {
            $data[$path] = $this->scopeConfig->getValue(
                $path,
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
        }

        return $data;
    }

    /**
     * Generate url to the cc image
     *
     * @param $file
     * @return string
     */
    protected function getImageUrl($file)
    {
        return $this->urlBuilder->getBaseUrl(['_type' => 'media']) . 'adyen/cc_logo/' . $file;
    }
}