<?php
namespace Deity\MagentoApi\Model\Integration;

use Deity\MagentoApi\Api\Integration\AdminTokenServiceInterface;
use Deity\MagentoApi\Api\Integration\Data\AdminTokenInterface;
use Deity\MagentoApi\Api\Integration\Data\AdminTokenInterfaceFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Integration\Model\AdminTokenService as MagentoAdminTokenService;
use Magento\Integration\Model\CredentialsValidator;
use Magento\Integration\Model\Oauth\TokenFactory as TokenModelFactory;
use Magento\Integration\Model\ResourceModel\Oauth\Token\CollectionFactory as TokenCollectionFactory;
use Magento\User\Model\User as UserModel;

class AdminTokenService extends MagentoAdminTokenService implements AdminTokenServiceInterface
{
    /** @var AdminTokenInterfaceFactory */
    private $adminTokenFactory;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /**
     * AdminTokenService constructor.
     * @param TokenModelFactory $tokenModelFactory
     * @param UserModel $userModel
     * @param TokenCollectionFactory $tokenModelCollectionFactory
     * @param CredentialsValidator $validatorHelper
     * @param AdminTokenInterfaceFactory $adminTokenFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        TokenModelFactory $tokenModelFactory,
        UserModel $userModel,
        TokenCollectionFactory $tokenModelCollectionFactory,
        CredentialsValidator $validatorHelper,
        AdminTokenInterfaceFactory $adminTokenFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($tokenModelFactory, $userModel, $tokenModelCollectionFactory, $validatorHelper);
        $this->adminTokenFactory = $adminTokenFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param string $username
     * @param string $password
     * @return AdminTokenInterface
     * @throws AuthenticationException
     */
    public function createAdminAccessToken($username, $password)
    {
        $token = parent::createAdminAccessToken($username, $password);

        /** @var AdminTokenInterface $adminToken */
        $adminToken = $this->adminTokenFactory->create();
        $adminToken->setToken($token);
        $adminToken->setValidTime((int)$this->scopeConfig->getValue('oauth/access_token_lifetime/admin'));

        return $adminToken;
    }
}