<?php

namespace Deity\MagentoApi\Plugin\Customer\Api;

use Deity\MagentoApi\Model\Cart\MergeManagement;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

class AccountManagement
{
    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var MergeManagement */
    private $cartMergeManagement;

    /** @var LoggerInterface */
    private $logger;


    /**
     * AccountManagement constructor.
     * @param CustomerRepositoryInterface $customerRepository
     * @param MergeManagement $cartMergeManagement
     * @param LoggerInterface $logger
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        MergeManagement $cartMergeManagement,
        LoggerInterface $logger
    )
    {
        $this->customerRepository = $customerRepository;
        $this->logger = $logger;
        $this->cartMergeManagement = $cartMergeManagement;
    }

    /**
     * @param AccountManagementInterface $subject
     * @param callable $proceed
     * @param CustomerInterface $customer
     * @param $hash
     * @param $redirectUrl
     * @return CustomerInterface
     */
    public function aroundCreateAccountWithPasswordHash(
        AccountManagementInterface $subject,
        callable $proceed,
        CustomerInterface $customer,
        $hash,
        $redirectUrl
    ) {
        $extensionAttributes = $customer->getExtensionAttributes();
        $quoteId = $extensionAttributes ? $extensionAttributes->getGuestQuoteId() : null;

        /** @var CustomerInterface $result */
        $customer = $proceed($customer, $hash, $redirectUrl);
        if ($quoteId) {
            try {
                $this->cartMergeManagement->convertGuestCart($quoteId, $customer);
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }

        return $customer;
    }

    /**
     * @param AccountManagementInterface $subject
     * @param callable $proceed
     * @param $email
     * @param $template
     * @param null $websiteId
     * @return bool
     */
    public function aroundInitiatePasswordReset(
        AccountManagementInterface $subject,
        callable $proceed,
        $email,
        $template,
        $websiteId = null
    ) {
        try {
            return $proceed($email, $template, $websiteId);
        } catch (NoSuchEntityException $e) {
            return true;
        }
    }

    /**
     * @param AccountManagementInterface $subject
     * @param string $email
     * @param string $resetToken
     * @param string $newPassword
     * @return array
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeResetPassword(AccountManagementInterface $subject, $email, $resetToken, $newPassword)
    {
        if (ctype_digit($email)) {
            $email = $this->customerRepository->getById($email)->getEmail();
        }

        return [$email, $resetToken, $newPassword];
    }
}
