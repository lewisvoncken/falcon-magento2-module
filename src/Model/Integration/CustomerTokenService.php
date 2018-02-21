<?php
namespace Deity\MagentoApi\Model\Integration;

use Deity\MagentoApi\Api\Integration\Data\CustomerTokenInterface;
use Deity\MagentoApi\Api\Integration\CustomerTokenServiceInterface;
use Deity\MagentoApi\Api\Integration\Data\CustomerTokenInterfaceFactory;
use Deity\MagentoApi\Model\Cart\MergeManagement;
use Exception;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Manager;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Integration\Model\CredentialsValidator;
use Magento\Integration\Model\CustomerTokenService as MagentoCustomerTokenService;
use Magento\Integration\Model\Oauth\Token\RequestThrottler;
use Magento\Integration\Model\Oauth\TokenFactory as TokenModelFactory;
use Magento\Integration\Model\ResourceModel\Oauth\Token\CollectionFactory as TokenCollectionFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Psr\Log\LoggerInterface;

class CustomerTokenService extends MagentoCustomerTokenService implements CustomerTokenServiceInterface
{
    /** @var CustomerTokenInterfaceFactory */
    private $customerTokenFactory;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var MergeManagement */
    private $cartMergeManagement;

    /** @var RequestThrottler */
    private $requestThrottler;

    /** @var QuoteIdMaskFactory */
    private $quoteIdMaskFactory;

    /** @var CartRepositoryInterface */
    private $cartRepository;

    /** @var Manager */
    private $eventManager;

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
     * @param RequestThrottler $requestThrottler
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param CartRepositoryInterface $cartRepository
     * @param Manager $eventManager
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
        RequestThrottler $requestThrottler,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        CartRepositoryInterface $cartRepository,
        Manager $eventManager,
        LoggerInterface $logger
    ) {
        parent::__construct($tokenModelFactory, $accountManagement, $tokenModelCollectionFactory, $validatorHelper);
        $this->customerTokenFactory = $customerTokenFactory;
        $this->scopeConfig = $scopeConfig;
        $this->cartMergeManagement = $cartMergeManagement;
        $this->requestThrottler = $requestThrottler;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->cartRepository = $cartRepository;
        $this->eventManager = $eventManager;
        $this->logger = $logger;
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $guestQuoteId
     * @return CustomerTokenInterface
     * @throws AuthenticationException
     * @throws LocalizedException
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

        /** @var CustomerTokenInterface $customerToken */
        $customerToken = $this->customerTokenFactory->create();
        $customerToken->setToken($token);
        $customerToken->setValidTime((int)$this->scopeConfig->getValue('oauth/access_token_lifetime/customer'));

        try {
            if ($this->shouldMergeCart($guestQuoteId)) {
                $this->cartMergeManagement->mergeGuestAndCustomerQuotes($guestQuoteId, $username);
            }
        } catch (Exception $e) {
            $this->logger->critical($e);
        }

        return $customerToken;
    }

    /**
     * Check if provided guestQuoteId is for active and guest cart to avoid duplicating cart
     *
     * @param string $guestQuoteId
     * @return CartInterface|false
     * @throws NoSuchEntityException
     */
    private function shouldMergeCart($guestQuoteId)
    {
        if (!$guestQuoteId) {
            return false;
        }
        /** @var QuoteIdMask $quoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create();
        $quoteIdMask->load($guestQuoteId, 'masked_id');

        /** @var CartInterface|Quote $guestCart */
        $guestCart = $this->cartRepository->getActive($quoteIdMask->getQuoteId());

        $cartToMerge = new DataObject();
        $cartToMerge->setData(
            'result',
            (!$guestCart->getCustomerId() ? $guestCart : false)
        );

        $this->eventManager->dispatch(
            'customer_generate_token_guest_cart_check',
            ['guest_cart' => $guestCart, 'cart_to_merge' => $cartToMerge]
        );

        return $cartToMerge->getData('result');
    }
}