<?php
namespace Hatimeria\Reagento\Model\Integration;

use Hatimeria\Reagento\Api\Integration\CustomerTokenServiceInterface;
use Hatimeria\Reagento\Api\Integration\Data\CustomerTokenInterface;
use Hatimeria\Reagento\Api\Integration\Data\CustomerTokenInterfaceFactory;
use Hatimeria\Reagento\Model\Cart\MergeManagement;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Integration\Model\CredentialsValidator;
use Magento\Integration\Model\CustomerTokenService as MagentoCustomerTokenService;
use Magento\Integration\Model\Oauth\TokenFactory as TokenModelFactory;
use Magento\Integration\Model\ResourceModel\Oauth\Token\CollectionFactory as TokenCollectionFactory;
use Psr\Log\LoggerInterface;

class CustomerTokenService extends MagentoCustomerTokenService implements CustomerTokenServiceInterface
{
    /** @var CustomerTokenInterfaceFactory */
    private $customerTokenFactory;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var MergeManagement */
    private $cartMergeManagement;

    /** @var LoggerInterface */
    private $logger;

    /**
     * CustomerTokenService constructor.
     * @param TokenModelFactory $tokenModelFactory
     * @param AccountManagementInterface $accountManagement
     * @param TokenCollectionFactory $tokenModelCollectionFactory
     * @param CredentialsValidator $validatorHelper
     * @param CustomerTokenInterfaceFactory $customerTokenFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param MergeManagement $cartMergeManagement
     * @param LoggerInterface $logger
     */
    public function __construct(
        TokenModelFactory $tokenModelFactory,
        AccountManagementInterface $accountManagement,
        TokenCollectionFactory $tokenModelCollectionFactory,
        CredentialsValidator $validatorHelper,
        CustomerTokenInterfaceFactory $customerTokenFactory,
        ScopeConfigInterface $scopeConfig,
        MergeManagement $cartMergeManagement,
        LoggerInterface $logger
    ) {
        parent::__construct($tokenModelFactory, $accountManagement, $tokenModelCollectionFactory, $validatorHelper);
        $this->customerTokenFactory = $customerTokenFactory;
        $this->scopeConfig = $scopeConfig;
        $this->cartMergeManagement = $cartMergeManagement;
        $this->logger = $logger;
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $guestQuoteId
     * @return CustomerTokenInterface
     * @throws AuthenticationException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function createCustomerAccessToken($username, $password, $guestQuoteId = null)
    {
        $token = parent::createCustomerAccessToken($username, $password);

        /** @var CustomerTokenInterface $customerToken */
        $customerToken = $this->customerTokenFactory->create();
        $customerToken->setToken($token);
        $customerToken->setValidTime((int)$this->scopeConfig->getValue('oauth/access_token_lifetime/customer'));

        if ($guestQuoteId) {
            $this->cartMergeManagement->mergeGuestAndCustomerQuotes($guestQuoteId, $username);
        }

        return $customerToken;
    }
}