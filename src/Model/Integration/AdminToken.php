<?php
namespace Hatimeria\Reagento\Model\Integration;

use Hatimeria\Reagento\Api\Integration\Data\AdminTokenInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

class AdminToken extends AbstractExtensibleObject implements AdminTokenInterface
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
     * @return \Hatimeria\Reagento\Api\Integration\Data\AdminTokenInterface
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
     * @return \Hatimeria\Reagento\Api\Integration\Data\AdminTokenInterface
     */
    public function setValidTime($time)
    {
        return $this->setData(self::VALID_TIME, $time);
    }

    /**
     * @return \Hatimeria\Reagento\Api\Integration\Data\AdminTokenExtensionInterface
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes() ?: $this->extensionFactory->create(AdminTokenInterface::class);
    }

    /**
     * @param \Hatimeria\Reagento\Api\Integration\Data\AdminTokenExtensionInterface $extensionAttributes
     * @return \Hatimeria\Reagento\Api\Integration\Data\AdminTokenInterface
     */
    public function setExtensionAttributes($extensionAttributes)
    {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}