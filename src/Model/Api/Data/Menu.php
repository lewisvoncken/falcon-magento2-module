<?php

namespace Deity\MagentoApi\Model\Api\Data;

use Deity\MagentoApi\Api\Data\MenuExtensionInterface;
use Deity\MagentoApi\Api\Data\MenuInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class Menu extends AbstractExtensibleModel implements MenuInterface
{
    /**
     * @return string
     */
    public function getName()
    {
        return $this->_getData(self::NAME);
    }

    /**
     * @param string $name
     * @return \Deity\MagentoApi\Api\Data\MenuInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->_getData(self::ID);
    }

    /**
     * @param int $id
     * @return \Deity\MagentoApi\Api\Data\MenuInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->_getData(self::URL);
    }

    /**
     * @param string $url
     * @return \Deity\MagentoApi\Api\Data\MenuInterface
     */
    public function setUrl($url)
    {
        return $this->setData(self::URL, $url);
    }

    /**
     * @return boolean
     */
    public function getHasActive()
    {
        return $this->_getData(self::HAS_ACTIVE);
    }

    /**
     * @param boolean $flag
     * @return \Deity\MagentoApi\Api\Data\MenuInterface
     */
    public function setHasActive($flag)
    {
        return $this->setData(self::HAS_ACTIVE, $flag);
    }

    /**
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->_getData(self::IS_ACTIVE);
    }

    /**
     * @param boolean $flag
     * @return \Deity\MagentoApi\Api\Data\MenuInterface
     */
    public function setIsActive($flag)
    {
        return $this->setData(self::IS_ACTIVE, $flag);
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->_getData(self::LEVEL);
    }

    /**
     * @param int $level
     * @return \Deity\MagentoApi\Api\Data\MenuInterface
     */
    public function setLevel($level)
    {
        return $this->setData(self::LEVEL, $level);
    }

    /**
     * @return boolean
     */
    public function getIsFirst()
    {
        return $this->_getData(self::IS_FIRST);
    }

    /**
     * @param boolean $flag
     * @return \Deity\MagentoApi\Api\Data\MenuInterface
     */
    public function setIsFirst($flag)
    {
        return $this->setData(self::IS_FIRST, $flag);
    }

    /**
     * @return boolean
     */
    public function getIsLast()
    {
        return $this->_getData(self::IS_LAST);
    }

    /**
     * @param boolean $flag
     * @return \Deity\MagentoApi\Api\Data\MenuInterface
     */
    public function setIsLast($flag)
    {
        return $this->setData(self::IS_LAST, $flag);
    }

    /**
     * @return string
     */
    public function getPositionClass()
    {
        return $this->_getData(self::POSITION_CLASS);
    }

    /**
     * @param string $class
     * @return \Deity\MagentoApi\Api\Data\MenuInterface
     */
    public function setPositionClass($class)
    {
        return $this->setData(self::POSITION_CLASS, $class);
    }

    /**
     * @return MenuInterface[]
     */
    public function getChildren()
    {
        return $this->_getData(self::CHILDREN) ?: [];
    }

    /**
     * @param MenuInterface[] $children
     * @return $this
     */
    public function setChildren($children)
    {
        return $this->setData(self::CHILDREN, $children);
    }

    /**
     * @return MenuExtensionInterface
     */
    public function getExtensionAttributes()
    {
        $extensionAttributes = $this->_getExtensionAttributes();
        if (!$extensionAttributes) {
            /** @var MenuExtensionInterface $extensionAttributes */
            $extensionAttributes = $this->extensionAttributesFactory->create(MenuInterface::class);
        }
        return $extensionAttributes;
    }

    /**
     * @param MenuExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(MenuExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}