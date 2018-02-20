<?php

namespace Deity\MagentoApi\Plugin\Store\Api;


use Magento\Store\Api\Data\StoreExtensionInterface;
use Magento\Store\Api\Data\StoreExtensionFactory;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\StoreRepositoryInterface;

class StoreRepository
{
    /**
     * @var StoreExtensionFactory
     */
    private $storeExtensionFactory;

    public function __construct(StoreExtensionFactory $storeExtensionFactory)
    {
        $this->storeExtensionFactory = $storeExtensionFactory;
    }

    /**
     * @param StoreRepositoryInterface $subject
     * @param StoreInterface[] $result
     * @return mixed
     */
    public function afterGetList(StoreRepositoryInterface $subject, $result)
    {
        foreach($result as $store) { /** @var StoreInterface $store */
            /** @var StoreExtensionInterface $extensionAttributes */
            $extensionAttributes = $store->getExtensionAttributes();
            if (!$extensionAttributes) {
                $extensionAttributes = $this->storeExtensionFactory->create();
            }
            $extensionAttributes->setIsActive($store->getIsActive());
            $store->setExtensionAttributes($extensionAttributes);
        }
        return $result;
    }
}