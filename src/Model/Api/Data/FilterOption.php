<?php

namespace Hatimeria\Reagento\Model\Api\Data;

use Hatimeria\Reagento\Api\Data\FilterOptionInterface;
use Hatimeria\Reagento\Api\Data\FilterOptionExtensionInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class FilterOption extends AbstractExtensibleModel implements FilterOptionInterface
{
    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->_getData(self::LABEL);
    }

    /**
     * @param string $label
     * @return \Hatimeria\Reagento\Api\Data\FilterOptionInterface
     */
    public function setLabel($label)
    {
        return $this->setData(self::LABEL, $label);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->_getData(self::VALUE);
    }

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\FilterOptionInterface
     */
    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
    }

    /**
     * @return boolean
     */
    public function getActive()
    {
        return $this->_getData(self::ACTIVE);
    }

    /**
     * @param boolean $active
     * @return \Hatimeria\Reagento\Api\Data\FilterOptionInterface
     */
    public function setActive($active)
    {
        return $this->setData(self::ACTIVE, $active);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        /** @var FilterOptionExtensionInterface $extensionAttributes */
        $extensionAttributes = $this->_getExtensionAttributes()
            ?: $this->extensionAttributesFactory->create(FilterOptionInterface::class);

        return $extensionAttributes;
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(FilterOptionExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}