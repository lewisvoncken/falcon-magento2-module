<?php

namespace Deity\MagentoApi\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * @package Deity\MagentoApi\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if(version_compare($context->getVersion(), '0.1.21') < 0) {
            $this->addOrderIdMask($setup);
        }

        $setup->endSetup();
    }

    protected function addOrderIdMask(SchemaSetupInterface $setup)
    {
        /**
         * Create table to store cartId and obscured UUID based cartId mapping
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('sales_order_id_mask')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        )->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Order ID'
        )->addIndex(
            $setup->getIdxName('sales_order_id_mask', ['order_id']),
            ['order_id']
        )->addForeignKey(
            $setup->getFkName('sales_order_id_mask', 'order_id', 'sales_order', 'entity_id'),
            'order_id',
            $setup->getTable('sales_order'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addColumn(
            'masked_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            ['nullable' => 'false'],
            'Masked ID'
        )->setComment(
            'Order ID and masked ID mapping'
        );

        $setup->getConnection()->createTable($table);
    }
}
