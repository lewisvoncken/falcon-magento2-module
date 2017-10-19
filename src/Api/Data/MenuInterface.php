<?php

namespace Hatimeria\Reagento\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Menu interface
 *
 * @package Hatimeria\Reagento\Api\Data
 * @api
 */
interface MenuInterface extends ExtensibleDataInterface
{
    const CHILDREN = 'children';
    const LEVEL = 'level';
    const LABEL = 'label';

    /**
     * @return \Hatimeria\Reagento\Api\Data\MenuInterface[]
     */
    public function getChildren();

    /**
     * @param \Hatimeria\Reagento\Api\Data\MenuInterface[] $children
     * @return mixed
     */
    public function setChildren($children);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Hatimeria\Reagento\Api\Data\MenuExtensionInterface
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Hatimeria\Reagento\Api\Data\MenuExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Hatimeria\Reagento\Api\Data\MenuExtensionInterface $extensionAttributes
    );

}