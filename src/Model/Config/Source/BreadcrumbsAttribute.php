<?php

namespace Deity\MagentoApi\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Option\ArrayInterface;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection as AttributeCollection;
/**
 * Breadcrumb attribute source model
 */
class BreadcrumbsAttribute implements OptionSourceInterface, ArrayInterface
{
    const BREADCRUMBS_ATTRIBUTES_CONFIG_PATH = 'deity/catalog/breadcrumbs_attributes';

    /** @var array $options */
    protected $options = [];

    /** @var AttributeCollectionFactory */
    protected $collectionFactory;

    /**
     * Breadcrumb attribute source model.
     *
     * @param AttributeCollectionFactory $collectionFactory
     */
    public function __construct(AttributeCollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Get Options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (empty($this->options)) {
            foreach($this->getProductAttributes() as $attribute) { /** @var Attribute $attribute */
                $this->options[] = [
                    'value' => $attribute->getData('attribute_code'),
                    'label' => $attribute->getData('frontend_label')
                ];
            }
        }

        return $this->options;
    }

    /**
     * Load available attributes from database
     */
    public function getProductAttributes()
    {
        /** @var AttributeCollection $attributeCollection */
        $attributeCollection = $this->collectionFactory->create();
        $attributeCollection->addFieldToFilter('main_table.backend_type', ['in' => ['int', 'varchar']]);

        return $attributeCollection->getItems();

    }
}