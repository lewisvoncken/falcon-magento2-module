<?php

namespace Hatimeria\Reagento\Api\Data;

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
     * @return \Hatimeria\Reagento\Api\Data\FilterInterface
     */
    public function setLabel($label);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     * @return \Hatimeria\Reagento\Api\Data\FilterInterface
     */
    public function setCode($code);

    /**
     * @return \Hatimeria\Reagento\Api\Data\FilterOptionInterface[]|null
     */
    public function getOptions();

    /**
     * @param \Hatimeria\Reagento\Api\Data\FilterOptionInterface[] $options
     * @return \Hatimeria\Reagento\Api\Data\FilterInterface
     */
    public function setOptions($options);

    /**
     * @return int|null
     */
    public function getAttributeId();

    /**
     * @param int $attributeId
     * @return \Hatimeria\Reagento\Api\Data\FilterInterface
     */
    public function setAttributeId($attributeId);

    /**
     * @return string|null
     */
    public function getType();

    /**
     * @param string $type
     * @return \Hatimeria\Reagento\Api\Data\FilterInterface;
     */
    public function setType($type);

    /**
     * @return \Hatimeria\Reagento\Api\Data\FilterExtensionInterface
     */
    public function getExtensionAttributes();

    /**
     * @param \Hatimeria\Reagento\Api\Data\FilterExtensionInterface $extensionAttributes
     * @return \Hatimeria\Reagento\Api\Data\FilterInterface
     */
    public function setExtensionAttributes(FilterExtensionInterface $extensionAttributes);

}