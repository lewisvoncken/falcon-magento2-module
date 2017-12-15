<?php

namespace Hatimeria\Reagento\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface FilterOptionInterface extends ExtensibleDataInterface
{
    const LABEL = 'label';
    const VALUE = 'value';
    const ACTIVE = 'active';

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string $label
     * @return \Hatimeria\Reagento\Api\Data\FilterOptionInterface
     */
    public function setLabel($label);

    /**
     * @return string
     */
    public function getValue();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\FilterOptionInterface
     */
    public function setValue($value);

    /**
     * @return boolean|null
     */
    public function getActive();

    /**
     * @param boolean $active
     * @return \Hatimeria\Reagento\Api\Data\FilterOptionInterface
     */
    public function setActive($active);

    /**
     * @return \Hatimeria\Reagento\Api\Data\FilterOptionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * @param \Hatimeria\Reagento\Api\Data\FilterOptionExtensionInterface $extensionAttributes
     * @return \Hatimeria\Reagento\Api\Data\FilterInterface
     */
    public function setExtensionAttributes(FilterOptionExtensionInterface $extensionAttributes);

}