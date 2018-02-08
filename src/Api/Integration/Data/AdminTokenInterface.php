<?php
namespace Hatimeria\Reagento\Api\Integration\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface AdminTokenInterface extends ExtensibleDataInterface
{
    const TOKEN = 'token';
    const VALID_TIME = 'valid_time';

    /**
     * @return string
     */
    public function getToken();

    /**
     * @param string $token
     * @return \Hatimeria\Reagento\Api\Integration\Data\AdminTokenInterface
     */
    public function setToken($token);

    /**
     * @return int
     */
    public function getValidTime();

    /**
     * @param int $time
     * @return \Hatimeria\Reagento\Api\Integration\Data\AdminTokenInterface
     */
    public function setValidTime($time);

    /**
     * @return \Hatimeria\Reagento\Api\Integration\Data\AdminTokenExtensionInterface
     */
    public function getExtensionAttributes();

    /**
     * @param \Hatimeria\Reagento\Api\Integration\Data\AdminTokenExtensionInterface $extensionAttributes
     * @return \Hatimeria\Reagento\Api\Integration\Data\AdminTokenInterface
     */
    public function setExtensionAttributes($extensionAttributes);
}