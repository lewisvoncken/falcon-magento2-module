<?php

namespace Hatimeria\Reagento\Plugin\Customer\Api;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\InputException;

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
