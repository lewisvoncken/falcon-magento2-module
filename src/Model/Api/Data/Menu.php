<?php

namespace Hatimeria\Reagento\Model\Api\Data;

use Hatimeria\Reagento\Api\Data\MenuExtensionInterface;
use Hatimeria\Reagento\Api\Data\MenuInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class Menu extends AbstractExtensibleModel implements MenuInterface
{
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