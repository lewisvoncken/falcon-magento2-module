<?php
namespace Deity\MagentoApi\Model\Customer;


use Deity\MagentoApi\Api\Customer\AddressRepositoryInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Customer\Api\AddressRepositoryInterface as CustomerAddressRepositoryInterface;
use Magento\Customer\Api\Data\AddressSearchResultsInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Model\Address;
use Magento\Customer\Model\AddressRegistry;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
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

    /** @var SearchCriteriaBuilder */
    protected $searchCriteriaBuilder;

    /**
     * AddressRepository constructor.
     * @param UserContextInterface $userContext
     * @param AddressRegistry $addressRegistry
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        UserContextInterface $userContext,
        AddressRegistry $addressRegistry,
        CustomerAddressRepositoryInterface $addressRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->userContext = $userContext;
        $this->addressRegistry = $addressRegistry;
        $this->addressRepository = $addressRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param SearchCriteriaInterface|null $searchCriteria
     * @return AddressSearchResultsInterface
     * @throws AuthorizationException
     * @throws LocalizedException
     */
    public function getCustomerAddressList(SearchCriteriaInterface $searchCriteria = null)
    {
        $this->checkCustomerContext();

        $searchCriteriaBuilder = $this->searchCriteriaBuilder;
        if ($searchCriteria) {
            $searchCriteriaBuilder->setCurrentPage($searchCriteria->getCurrentPage());
            $searchCriteriaBuilder->setPageSize($searchCriteria->getPageSize());
            $searchCriteriaBuilder->setFilterGroups($searchCriteria->getFilterGroups());
            $searchCriteriaBuilder->setSortOrders($searchCriteria->getSortOrders() ?: []);
        }
        $customerId = $this->getCurrentCustomerId();
        $searchCriteriaBuilder = $this->searchCriteriaBuilder->addFilter('parent_id', $customerId);
        /** @var AddressSearchResultsInterface $searchResult */
        $searchResult = $this->addressRepository->getList($searchCriteriaBuilder->create());

        foreach($searchResult->getItems() as $item) {
            $this->ensureDefaultAddressFlags($item);
        }
        return $searchResult;
    }

    /**
     * @param int $addressId
     * @return AddressInterface
     * @throws AuthorizationException
     * @throws NoSuchEntityException
     */
    public function getCustomerAddress($addressId)
    {
        $this->checkCustomerContext();
        $addressModel = $this->addressRegistry->retrieve($addressId);
        if ($addressModel->getCustomerId() !== $this->getCurrentCustomerId()) {
            throw new AuthorizationException(__('Customer is not allowed to view this address'));
        }

        return $this->ensureDefaultAddressFlags($addressModel->getDataModel());
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
        return $this->ensureDefaultAddressFlags($this->addressRepository->save($address));
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
        return $this->ensureDefaultAddressFlags($this->addressRepository->save($address));
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

    /**
     * @param AddressInterface $customerAddress
     * @return AddressInterface
     */
    protected function ensureDefaultAddressFlags(AddressInterface $customerAddress)
    {
        if (!$customerAddress->isDefaultBilling()) {
            $customerAddress->setIsDefaultBilling(false);
        }
        if (!$customerAddress->isDefaultShipping()) {
            $customerAddress->setIsDefaultShipping(false);
        }

        return $customerAddress;
    }
}