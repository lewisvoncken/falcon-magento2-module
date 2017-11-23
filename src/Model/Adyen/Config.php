<?php

namespace Hatimeria\Reagento\Model\Adyen;

use Hatimeria\Reagento\Api\Data\AdyenConfigInterface;
use Hatimeria\Reagento\Api\Data\AdyenConfigInterfaceFactory;
use Hatimeria\Reagento\Api\HatimeriaAdyenConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Config implements HatimeriaAdyenConfigInterface
{
    const ADYEN_CONFIG_MODE = 'payment/adyen_abstract/demo_mode';
    const ADYEN_CSE_CONFIG_LIVE = 'payment/adyen_cc/cse_publickey_live';
    const ADYEN_CSE_CONFIG_TEST = 'payment/adyen_cc/cse_publickey_test';
    const ADYEN_CC_ENABLED = 'payment/adyen_cc/active';
    const ADYEN_CC_TYPES = 'payment/adyen_cc/cctypes';

    /** @var StoreManagerInterface */
    protected $storeManager;

    /** @var ScopeConfigInterface */
    protected $scopeConfig;

    /** @var AdyenConfigInterfaceFactory */
    protected $adyenConfigFactory;

    /**
     * Config constructor.
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param AdyenConfigInterfaceFactory $adyenConfigFactory
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        AdyenConfigInterfaceFactory $adyenConfigFactory
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->adyenConfigFactory = $adyenConfigFactory;
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
            $csePath
        ];

        $configs = $this->fetchConfig($configPaths, $storeId);

        $adyenConfig = $this->getAdyenConfigDataObject();
        $adyenConfig->setCcEnabled($configs[self::ADYEN_CC_ENABLED]);
        $adyenConfig->setCcAvailableCards($configs[self::ADYEN_CC_TYPES]);
        $adyenConfig->setCsePublicKey($configs[$csePath ]);

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
}