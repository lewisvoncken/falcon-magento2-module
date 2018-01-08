<?php

namespace Hatimeria\Reagento\Plugin\Customer\Api;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Psr\Log\LoggerInterface;

class AccountManagement
{
    /** @var CustomerRepositoryInterface */
    private $customerRepository;

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
        CartRepositoryInterface $cartRepository,
        LoggerInterface $logger
    )
    {
        $this->customerRepository = $customerRepository;
        $this->cartRepository = $cartRepository;
        $this->logger = $logger;
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
        $quoteId = $customer->getExtensionAttributes()->getGuestQuoteId();

        /** @var CustomerInterface $result */
        $customer = $proceed($customer, $hash, $redirectUrl);
        if ($quoteId) {
            try {
                /** @var Quote $customerQuote */
                $customerQuote = $this->cartRepository->getActiveForCustomer($customer->getId());
                /** @var Quote $guestQuote */
                $guestQuote = $this->cartRepository->getActive($quoteId);

                $customerQuote->merge($guestQuote);
                $this->cartRepository->save($customerQuote);
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
