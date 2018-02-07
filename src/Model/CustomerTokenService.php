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
use Magento\Integration\Model\Oauth\Token\RequestThrottler;
use Magento\Integration\Model\Oauth\TokenFactory as TokenModelFactory;
use Magento\Integration\Model\ResourceModel\Oauth\Token\CollectionFactory as TokenCollectionFactory;
use Psr\Log\LoggerInterface;

class CustomerTokenService extends MagentoCustomerTokenService implements CustomerTokenServiceInterface
{
    /** @var MergeManagement */
    private $cartMergeManagement;

    /** @var LoggerInterface */
    private $logger;

    /** @var RequestThrottler */
    private $requestThrottler;

    /**
     * CustomerTokenService constructor.
     * @param TokenModelFactory $tokenModelFactory
     * @param AccountManagementInterface $accountManagement
     * @param TokenCollectionFactory $tokenModelCollectionFactory
     * @param CredentialsValidator $validatorHelper
     * @param MergeManagement $cartMergeManagement
     * @param RequestThrottler $requestThrottler
     * @param LoggerInterface $logger
     */
    public function __construct(
        TokenModelFactory $tokenModelFactory,
        AccountManagementInterface $accountManagement,
        TokenCollectionFactory $tokenModelCollectionFactory,
        CredentialsValidator $validatorHelper,
        MergeManagement $cartMergeManagement,
        RequestThrottler $requestThrottler,
        LoggerInterface $logger
    ) {
        parent::__construct($tokenModelFactory, $accountManagement, $tokenModelCollectionFactory, $validatorHelper);
        $this->cartMergeManagement = $cartMergeManagement;
        $this->logger = $logger;
        $this->requestThrottler = $requestThrottler;
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
        //first check if account is not locked and if so change the message
        try {
            $this->requestThrottler->throttle($username, RequestThrottler::USER_TYPE_CUSTOMER);
        } catch (AuthenticationException $e) {
            throw new AuthenticationException(__('Your account is temporarily disabled due to too many failed attempt. Please try again later.'), $e);
        }

        try {
            $token = parent::createCustomerAccessToken($username, $password);
        } catch (AuthenticationException $e) {
            //we know account is not locked so this is incorrect password or invalid username
            throw new AuthenticationException(__('You did not sign in correctly or your account is not active.'), $e);
        }

        if ($guestQuoteId) {
            $this->cartMergeManagement->mergeGuestAndCustomerQuotes($guestQuoteId, $username);
        }

        return $token;
    }
}