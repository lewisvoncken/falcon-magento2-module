<?php
namespace Deity\MagentoApi\Model\Security;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Exception\AuthorizationException;

class CustomerContext
{
    /** @var UserContextInterface */
    private $userContext;

    /**
     * @param UserContextInterface $userContext
     */
    public function __construct(UserContextInterface $userContext)
    {
        $this->userContext = $userContext;
    }

    /**
     * Get current user id
     *
     * @return int
     */
    public function getCurrentCustomerId()
    {
        return (int)$this->userContext->getUserId();
    }

    /**
     * Check if current user context is for logged in customer
     *
     * @param int $customerId
     * @return bool
     * @throws AuthorizationException
     */
    public function checkCustomerContext($customerId = null)
    {
        if ($this->userContext->getUserType() !== UserContextInterface::USER_TYPE_CUSTOMER) {
            throw new AuthorizationException(__('This method is available only for customer tokens'));
        }

        if ($customerId && $this->getCurrentCustomerId() !== $customerId) {
            throw new AuthorizationException(__('You are not authorized to perform this action'));
        }

        return true;
    }
}