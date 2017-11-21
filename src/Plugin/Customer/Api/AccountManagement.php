<?php

namespace Hatimeria\Reagento\Plugin\Customer\Api;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;

class AccountManagement
{
    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /**
     * AccountManagement constructor.
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository
    )
    {
        $this->customerRepository = $customerRepository;
    }

    public function aroundInitiatePasswordReset(
        AccountManagementInterface $subject,
        callable $proceed,
        $email,
        $template,
        $websiteId = null
    ) {
        try {
            return $proceed($email, $email, $websiteId);
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
     * @throws InputException
     */
    public function beforeResetPassword(AccountManagementInterface $subject, $email, $resetToken, $newPassword)
    {
        if (ctype_digit($email)) {
            $email = $this->customerRepository->getById($email)->getEmail();
        }

        return [$email, $resetToken, $newPassword];
    }
}
