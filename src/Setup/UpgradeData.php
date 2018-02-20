<?php

namespace Deity\MagentoApi\Setup;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * @package Deity\MagentoApi\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create();

        if(version_compare($context->getVersion(), '0.0.1') < 0) {
            $this->addCategoryShowOnHomeField($eavSetup);
        }
        if(version_compare($context->getVersion(), '0.1.20') < 0) {
            $this->addCategoryShowOnHomePositionField($setup, $eavSetup);
        }
        if(version_compare($context->getVersion(), '0.1.22') < 0) {
            $this->addProductShowOnHomePositionField($setup, $eavSetup);
        }

        $setup->endSetup();
    }

    /**
     * @param EavSetup $eavSetup
     */
    protected function addCategoryShowOnHomeField($eavSetup)
    {
        /**
         * Add attributes to the eav/attribute
         */
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'is_on_homepage',
            [
                'type' => 'int',
                'label' => 'Show on homepage',
                'input' => 'select',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'default' => '1',
                'sort_order' => 19,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'General Information',
                'required' => false,
                'user_defined' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => true,
            ]
        );
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param EavSetup $eavSetup
     */
    protected function addCategoryShowOnHomePositionField(ModuleDataSetupInterface $setup, EavSetup $eavSetup)
    {
        /**
         * Add attributes to the eav/attribute
         */
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'homepage_position',
            [
                'type' => 'int',
                'label' => 'Homepage Position',
                'input' => 'text',
                'source' => '',
                'default' => 1000,
                'sort_order' => 20,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'General Information',
                'required' => false,
                'user_defined' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => true,
            ]
        );

        $attributeId = $eavSetup->getAttributeId(Category::ENTITY, 'homepage_position');

        /** @var AdapterInterface $connection */
        $connection = $setup->getConnection();

        $select = $connection->select()->from(['category' => $connection->getTableName('catalog_category_entity_int')], 'category.entity_id AS category_id')
                            ->join(['main' => $connection->getTableName('catalog_category_entity')], 'main.entity_id=category.entity_id', null)
                            ->join(['eav' => $connection->getTableName('eav_attribute')], 'eav.attribute_id=category.attribute_id', null)
                            ->where('eav.entity_type_id = ?', 3)
                            ->where('eav.attribute_code = ?', 'is_on_homepage')
                            ->where('category.value = ?', 1)
                            ->order(['main.level ASC', 'main.position ASC']);

        $data = [];
        $position = 100;
        foreach($connection->fetchAll($select) as $row) {
            $position += 100;
            $data[] = [
                'entity_id' => $row['category_id'],
                'attribute_id' => $attributeId,
                'store_id' => 0,
                'value' => $position
            ];
        }

        $connection->insertOnDuplicate($connection->getTableName('catalog_category_entity_int'), $data, []);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param EavSetup $eavSetup
     */
    protected function addProductShowOnHomePositionField(ModuleDataSetupInterface $setup, EavSetup $eavSetup)
    {
        $eavSetup->addAttribute(
            Product::ENTITY,
            'is_on_homepage',
            [
                'type' => 'int',
                'input' => 'boolean',
                'label' => 'On homepage',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required' => false,
                'searchable' => true,
                'default' => 0,
                'filterable' => true,
                'is_filterable_in_grid' => true,
                'is_used_in_grid' => false,
                'is_searchable' => true,
                'is_visible_in_grid' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'user_defined' => true,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Product Details',
                'sort_order' => 1,
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'homepage_sort_order',
            [
                'type' => 'int',
                'input' => 'text',
                'label' => 'Homepage Product Sort Order',
                'required' => false,
                'searchable' => true,
                'default' => 100,
                'filterable' => true,
                'is_filterable_in_grid' => true,
                'is_used_in_grid' => false,
                'is_searchable' => true,
                'is_visible_in_grid' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'user_defined' => true,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Product Details',
                'sort_order' => 1,
            ]
        );

        foreach($eavSetup->getAllAttributeSetIds(Product::ENTITY) as $setId) {
            $eavSetup->addAttributeToGroup(
                ProductAttributeInterface::ENTITY_TYPE_CODE,
                $setId,
                'Product Details',
                'is_on_homepage',
                100
            );

            $eavSetup->addAttributeToGroup(
                ProductAttributeInterface::ENTITY_TYPE_CODE,
                $setId,
                'Product Details',
                'homepage_sort_order',
                101
            );
        }
    }
}
