<?php

namespace Deity\MagentoApi\Model;

use Deity\MagentoApi\Api\Data\InfoDataInterface;
use Deity\MagentoApi\Api\InfoInterface;

/**
 * @package Deity\MagentoApi\Model
 */
class Info implements InfoInterface
{
    private $dataFactory;
    private $customerUrl;

    public function __construct(
        \Deity\MagentoApi\Api\Data\InfoDataInterfaceFactory $dataFactory,
        \Magento\Customer\Model\Url $customerUrl
    ) {
        $this->dataFactory = $dataFactory;
        $this->customerUrl = $customerUrl;
    }

    /**
     * @return \Deity\MagentoApi\Api\Data\InfoDataInterface
     */
    public function getInfo()
    {
        /** @var InfoDataInterface $infoData */
        $infoData = $this->dataFactory->create();

        $infoData->setCustomerRegisterUrl($this->customerUrl->getRegisterUrl());
        $infoData->setCustomerDashboardUrl($this->customerUrl->getDashboardUrl());

        return $infoData;
    }
}