<?php
namespace Hatimeria\Reagento\Model;

use Hatimeria\Reagento\Api\CustomerTokenServiceInterface;
use Hatimeria\Reagento\Model\Cart\MergeManagement;
use Magento\Customer\Api\AccountManagementInterface;
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
     * @param MergeManagement $cartMergeManagement
     * @param LoggerInterface $logger
     */
    public function __construct(
        TokenModelFactory $tokenModelFactory,
        AccountManagementInterface $accountManagement,
        TokenCollectionFactory $tokenModelCollectionFactory,
        CredentialsValidator $validatorHelper,
        MergeManagement $cartMergeManagement,
        LoggerInterface $logger
    ) {
        parent::__construct($tokenModelFactory, $accountManagement, $tokenModelCollectionFactory, $validatorHelper);
        $this->cartMergeManagement = $cartMergeManagement;
        $this->logger = $logger;
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $guestQuoteId
     * @return string
     * @throws AuthenticationException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function createCustomerAccessToken($username, $password, $guestQuoteId = null)
    {
        $token = parent::createCustomerAccessToken($username, $password);

        if ($guestQuoteId) {
            $this->cartMergeManagement->mergeGuestAndCustomerQuotes($guestQuoteId, $username);
        }

        return $token;
    }
}