<?php

namespace Hatimeria\Reagento\Api;

interface HatimeriaOrderManagementInterface
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
}