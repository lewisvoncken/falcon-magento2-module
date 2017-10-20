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
    const NAME = 'name';
    const ID = 'id';
    const URL = 'url';
    const HAS_ACTIVE = 'has_active';
    const IS_ACTIVE = 'is_active';
    const LEVEL = 'level';
    const IS_FIRST = 'is_first';
    const IS_LAST = 'is_last';
    const POSITION_CLASS = 'position_class';

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return \Hatimeria\Reagento\Api\Data\MenuInterface
     */
    public function setName($name);

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return \Hatimeria\Reagento\Api\Data\MenuInterface
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param string $url
     * @return \Hatimeria\Reagento\Api\Data\MenuInterface
     */
    public function setUrl($url);

    /**
     * @return boolean
     */
    public function getHasActive();

    /**
     * @param boolean $flag
     * @return \Hatimeria\Reagento\Api\Data\MenuInterface
     */
    public function setHasActive($flag);

    /**
     * @return boolean
     */
    public function getIsActive();

    /**
     * @param boolean $flag
     * @return \Hatimeria\Reagento\Api\Data\MenuInterface
     */
    public function setIsActive($flag);

    /**
     * @return int
     */
    public function getLevel();

    /**
     * @param int $level
     * @return \Hatimeria\Reagento\Api\Data\MenuInterface
     */
    public function setLevel($level);

    /**
     * @return boolean
     */
    public function getIsFirst();

    /**
     * @param boolean $flag
     * @return \Hatimeria\Reagento\Api\Data\MenuInterface
     */
    public function setIsFirst($flag);

    /**
     * @return boolean
     */
    public function getIsLast();

    /**
     * @param boolean $flag
     * @return \Hatimeria\Reagento\Api\Data\MenuInterface
     */
    public function setIsLast($flag);

    /**
     * @return string
     */
    public function getPositionClass();

    /**
     * @param string $class
     * @return \Hatimeria\Reagento\Api\Data\MenuInterface
     */
    public function setPositionClass($class);
    
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