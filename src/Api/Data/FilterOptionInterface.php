<?php

namespace Deity\MagentoApi\Api\Data;

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
     * @return \Deity\MagentoApi\Api\Data\FilterOptionInterface
     */
    public function setLabel($label);

    /**
     * @return string
     */
    public function getValue();

    /**
     * @param string $value
     * @return \Deity\MagentoApi\Api\Data\FilterOptionInterface
     */
    public function setValue($value);

    /**
     * @return boolean|null
     */
    public function getActive();

    /**
     * @param boolean $active
     * @return \Deity\MagentoApi\Api\Data\FilterOptionInterface
     */
    public function setActive($active);

    /**
     * @return \Deity\MagentoApi\Api\Data\FilterOptionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * @param \Deity\MagentoApi\Api\Data\FilterOptionExtensionInterface $extensionAttributes
     * @return \Deity\MagentoApi\Api\Data\FilterInterface
     */
    public function setExtensionAttributes(FilterOptionExtensionInterface $extensionAttributes);

}