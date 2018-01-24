<?php
namespace Hatimeria\Reagento\Model\Customer;


use Hatimeria\Reagento\Api\Customer\AddressRepositoryInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Customer\Api\AddressRepositoryInterface as CustomerAddressRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Model\Address;
use Magento\Customer\Model\AddressRegistry;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class AddressRepository implements AddressRepositoryInterface
{
    /** @var UserContextInterface */
    protected $userContext;

    /** @var AddressRegistry */
    protected $addressRegistry;

    /** @var CustomerAddressRepositoryInterface */
    protected $addressRepository;

    /**
     * AddressRepository constructor.
     * @param UserContextInterface $userContext
     * @param AddressRegistry $addressRegistry
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     */
    public function __construct(
        UserContextInterface $userContext,
        AddressRegistry $addressRegistry,
        CustomerAddressRepositoryInterface $addressRepository
    ) {
        $this->userContext = $userContext;
        $this->addressRegistry = $addressRegistry;
        $this->addressRepository = $addressRepository;
    }

    /**
     * @param AddressInterface $address
     * @return AddressInterface
     * @throws AuthorizationException
     * @throws LocalizedException
     */
    public function createCustomerAddress(AddressInterface $address)
    {
        $this->checkCustomerContext();
        $address->setId(null);
        $address->setCustomerId($this->getCurrentCustomerId());
        return $this->addressRepository->save($address);
    }

    /**
     * @param AddressInterface $address
     * @return AddressInterface
     * @throws LocalizedException
     */
    public function updateCustomerAddress(AddressInterface $address)
    {
        $this->checkCustomerContext();
        if (!$address->getId()) {
            throw new InputException(__('Provided address does not exists'));
        }
        $addressModel = $this->addressRegistry->retrieve($address->getId());
        if ($addressModel->getCustomerId() !== $this->getCurrentCustomerId()) {
            throw new AuthorizationException(__('Customer is not allowed to update this address'));
        }

        $address->setCustomerId($this->getCurrentCustomerId());
        return $this->addressRepository->save($address);
    }

    /**
     * @param int $addressId
     * @return bool
     * @throws AuthorizationException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function deleteCustomerAddress($addressId)
    {
        $this->checkCustomerContext();
        /** @var Address $address */
        $address = $this->addressRegistry->retrieve($addressId);
        if ($address->getCustomerId() !== $this->getCurrentCustomerId()) {
            throw new AuthorizationException(__('Customer is not allowed to delete this address'));
        }

        return $this->addressRepository->deleteById($addressId);
    }

    /**
     * Get current user id
     *
     * @return int
     */
    protected function getCurrentCustomerId()
    {
        return $this->userContext->getUserId();
    }

    /**
     * Check if current user context is for logged in customer
     *
     * @return bool
     * @throws AuthorizationException
     */
    private function checkCustomerContext()
    {
        if ($this->userContext->getUserType() !== UserContextInterface::USER_TYPE_CUSTOMER) {
            throw new AuthorizationException(__('This method is available only for customer tokens'));
        }

        return true;
    }
}