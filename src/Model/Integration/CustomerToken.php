<?php
namespace Deity\MagentoApi\Model\Integration;

use Deity\MagentoApi\Api\Integration\Data\CustomerTokenInterface;
use Deity\MagentoApi\Api\Integration\Data\CustomerTokenExtensionInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

class CustomerToken extends AbstractExtensibleObject implements CustomerTokenInterface
{
    /**
     * @return string
     */
    public function getToken()
    {
        return $this->_get(self::TOKEN);
    }

    /**
     * @param string $token
     * @return \Deity\MagentoApi\Api\Integration\Data\CustomerTokenInterface
     */
    public function setToken($token)
    {
        return $this->setData(self::TOKEN, $token);
    }

    /**
     * @return int
     */
    public function getValidTime()
    {
        return $this->_get(self::VALID_TIME);
    }

    /**
     * @param $time
     * @return \Deity\MagentoApi\Api\Integration\Data\CustomerTokenInterface
     */
    public function setValidTime($time)
    {
        return $this->setData(self::VALID_TIME, $time);
    }

    /**
     * @return \Deity\MagentoApi\Api\Integration\Data\CustomerTokenExtensionInterface
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes() ?: $this->extensionFactory->create(CustomerTokenInterface::class);
    }

    /**
     * @param \Deity\MagentoApi\Api\Integration\Data\CustomerTokenExtensionInterface $extensionAttributes
     * @return \Deity\MagentoApi\Api\Integration\Data\CustomerTokenInterface
     */
    public function setExtensionAttributes(CustomerTokenExtensionInterface $extensionAttributes)
    {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}