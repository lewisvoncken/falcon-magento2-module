<?php
namespace Deity\MagentoApi\Api\Integration;

use Magento\Integration\Api\AdminTokenServiceInterface as MagentoAdminTokenServiceInterface;

interface AdminTokenServiceInterface extends MagentoAdminTokenServiceInterface
{

    /**
     * Create access token for admin given the admin credentials.
     *
     * @param string $username
     * @param string $password
     * @return \Deity\MagentoApi\Api\Integration\Data\AdminTokenInterface Token created
     * @throws \Magento\Framework\Exception\InputException For invalid input
     * @throws \Magento\Framework\Exception\AuthenticationException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createAdminAccessToken($username, $password);
}