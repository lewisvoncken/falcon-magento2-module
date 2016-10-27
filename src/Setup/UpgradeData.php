<?php

namespace Hatimeria\Reagento\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * @package Hatimeria\Reagento\Setup
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

        $setup->endSetup();
    }
}
