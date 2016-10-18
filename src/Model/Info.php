<?php

namespace Hatimeria\Reagento\Model;

use Hatimeria\Reagento\Api\Data\InfoDataInterface;
use Hatimeria\Reagento\Api\InfoInterface;

/**
 * @package Hatimeria\Reagento\Model
 */
class Info implements InfoInterface
{
    private $dataFactory;
    private $customerUrl;

    public function __construct(
        \Hatimeria\Reagento\Api\Data\InfoDataInterfaceFactory $dataFactory,
        \Magento\Customer\Model\Url $customerUrl
    ) {
        $this->dataFactory = $dataFactory;
        $this->customerUrl = $customerUrl;
    }

    /**
     * @return \Hatimeria\Reagento\Api\Data\InfoDataInterface
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