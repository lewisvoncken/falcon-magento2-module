<?php

namespace Deity\MagentoApi\Api\Sales;

interface OrderManagementInterface
{
    /**
     * @param mixed $orderId
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function getItem($orderId);

    /**
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magento\Sales\Api\Data\OrderSearchResultInterface
     */
    public function getCustomerOrders(\Magento\Framework\Api\SearchCriteria $searchCriteria);

    /**
     * @param string $paypalHash
     * @return int
     */
    public function getOrderIdFromHash($paypalHash);
}