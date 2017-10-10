<?php

namespace Hatimeria\Reagento\Model\Plugin;

use Magento\Directory\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Api\Data\StoreConfigInterface;
use Magento\Store\Api\StoreConfigManagerInterface;
use Magento\Store\Api\Data\StoreConfigExtensionInterface;
use Magento\Store\Api\Data\StoreConfigExtensionFactory;


class AfterGetStoreConfigs
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreConfigExtensionFactory
     */
    protected $storeConfigExtensionFactory;

    /**
     * AfterGetStoreConfigs constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreConfigExtensionFactory $storeConfigExtensionFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreConfigExtensionFactory $storeConfigExtensionFactory
    ) {

        $this->scopeConfig = $scopeConfig;
        $this->storeConfigExtensionFactory = $storeConfigExtensionFactory;
    }

    /**
     * @param StoreConfigManagerInterface $subject
     * @param StoreConfigInterface[] $result
     * @return StoreConfigInterface[]
     */
    public function afterGetStoreConfigs(StoreConfigManagerInterface $subject, $result)
    {
        $optionalZipCodeCountries = $this->scopeConfig->getValue(Data::OPTIONAL_ZIP_COUNTRIES_CONFIG_PATH);
        if (!empty($optionalZipCodeCountries)) {
            $optionalZipCodeCountries = explode(',', $optionalZipCodeCountries);
        }

        foreach($result as $item) { /** @var StoreConfigInterface $item */
            /** @var StoreConfigExtensionInterface $extensionAttributes */
            $extensionAttributes = $item->getExtensionAttributes();
            if (!$extensionAttributes) {
                $extensionAttributes = $this->storeConfigExtensionFactory->create();
            }
            $extensionAttributes->setOptionalPostCodes($optionalZipCodeCountries);
            $item->setExtensionAttributes($extensionAttributes);
        }

        return $result;
    }
}
