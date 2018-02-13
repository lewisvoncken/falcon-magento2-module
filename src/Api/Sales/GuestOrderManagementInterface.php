<?php

namespace Deity\MagentoApi\Api\Sales;

interface GuestOrderManagementInterface
{
    /**
     * @param mixed $orderId
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function getItem($orderId);
}