<?php

namespace Deity\MagentoApi\Model\ResourceModel\Order;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * OrderIdMask Resource model
 * @codeCoverageIgnore
 */
class OrderIdMask extends AbstractDb
{
    /**
     * Main table and field initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sales_order_id_mask', 'entity_id');
    }
}
