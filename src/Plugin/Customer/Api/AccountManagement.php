<?php

namespace Hatimeria\Reagento\Plugin\Customer\Api;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Psr\Log\LoggerInterface;

class AccountManagement
{
    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var QuoteIdMaskFactory */
    private $quoteIdMaskFactory;

    /** @var CartRepositoryInterface */
    private $cartRepository;

    /** @var LoggerInterface */
    private $logger;

    /**
     * AccountManagement constructor.
     * @param CustomerRepositoryInterface $customerRepository
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        CartRepositoryInterface $cartRepository,
        LoggerInterface $logger
    )
    {
        $this->customerRepository = $customerRepository;
        $this->cartRepository = $cartRepository;
        $this->logger = $logger;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
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
                /** @var QuoteIdMask $quoteIdMask */
                $quoteIdMask = $this->quoteIdMaskFactory->create();
                $quoteIdMask->load($quoteId, 'masked_id');
                /** @var Quote $guestQuote */
                $guestQuote = $this->cartRepository->getActive($quoteIdMask->getQuoteId());
                $guestQuote->assignCustomer($customer);
                $guestQuote->setCheckoutMethod(null);
                $this->cartRepository->save($guestQuote);
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
