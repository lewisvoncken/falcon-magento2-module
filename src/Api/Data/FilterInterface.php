<?php

namespace Deity\MagentoApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\Phrase;

interface FilterInterface extends ExtensibleDataInterface
{
    const LABEL = 'label';
    const CODE  = 'code';
    const OPTIONS = 'options';
    const ATTRIBUTE_ID = 'attribute_id';
    const TYPE = 'type';

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string|Phrase $label
     * @return \Deity\MagentoApi\Api\Data\FilterInterface
     */
    public function setLabel($label);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     * @return \Deity\MagentoApi\Api\Data\FilterInterface
     */
    public function setCode($code);

    /**
     * @return \Deity\MagentoApi\Api\Data\FilterOptionInterface[]|null
     */
    public function getOptions();

    /**
     * @param \Deity\MagentoApi\Api\Data\FilterOptionInterface[] $options
     * @return \Deity\MagentoApi\Api\Data\FilterInterface
     */
    public function setOptions($options);

    /**
     * @return int|null
     */
    public function getAttributeId();

    /**
     * @param int $attributeId
     * @return \Deity\MagentoApi\Api\Data\FilterInterface
     */
    public function setAttributeId($attributeId);

    /**
     * @return string|null
     */
    public function getType();

    /**
     * @param string $type
     * @return \Deity\MagentoApi\Api\Data\FilterInterface;
     */
    public function setType($type);

    /**
     * @return \Deity\MagentoApi\Api\Data\FilterExtensionInterface
     */
    public function getExtensionAttributes();

    /**
     * @param \Deity\MagentoApi\Api\Data\FilterExtensionInterface $extensionAttributes
     * @return \Deity\MagentoApi\Api\Data\FilterInterface
     */
    public function setExtensionAttributes(FilterExtensionInterface $extensionAttributes);

}