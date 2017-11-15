<?php

namespace Hatimeria\Reagento\Plugin\Customer\Api;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Customer\CredentialsValidator;
use Magento\Framework\Exception\InputException;

class AccountManagement
{
    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var CredentialsValidator */
    private $credentialsValidator;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        CredentialsValidator $credentialsValidator
    )
    {
        $this->customerRepository = $customerRepository;
        $this->credentialsValidator = $credentialsValidator;
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

        $this->credentialsValidator->checkPasswordDifferentFromEmail($email, $newPassword);

        return [$email, $resetToken, $newPassword];
    }
}
