<?php
namespace Deity\MagentoApi\Api\Integration\Data;

use Deity\MagentoApi\Api\Integration\Data\CustomerTokenExtensionInterface;
use Magento\Framework\Api\ExtensibleDataInterface;

interface CustomerTokenInterface extends ExtensibleDataInterface
{
    const TOKEN = 'token';
    const VALID_TIME = 'valid_time';

    /**
     * @return string
     */
    public function getToken();

    /**
     * @param string $token
     * @return \Deity\MagentoApi\Api\Integration\Data\AdminTokenInterface
     */
    public function setToken($token);

    /**
     * @return int
     */
    public function getValidTime();

    /**
     * @param int $time
     * @return \Deity\MagentoApi\Api\Integration\Data\AdminTokenInterface
     */
    public function setValidTime($time);

    /**
     * @return \Deity\MagentoApi\Api\Integration\Data\CustomerTokenExtensionInterface
     */
    public function getExtensionAttributes();

    /**
     * @param \Deity\MagentoApi\Api\Integration\Data\CustomerTokenExtensionInterface $extensionAttributes
     * @return \Deity\MagentoApi\Api\Integration\Data\AdminTokenInterface
     */
    public function setExtensionAttributes(CustomerTokenExtensionInterface $extensionAttributes);
}