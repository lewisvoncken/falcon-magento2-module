<?php
namespace Deity\MagentoApi\Model\Customer;


use Deity\MagentoApi\Api\Customer\AddressRepositoryInterface;
use Deity\MagentoApi\Model\Security\CustomerContext;
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

    /** @var CustomerContext */
    private $customerContext;

    /** @var AddressRegistry */
    private $addressRegistry;

    /** @var CustomerAddressRepositoryInterface */
    private $addressRepository;

    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /**
     * AddressRepository constructor.
     * @param CustomerContext $customerContext
     * @param AddressRegistry $addressRegistry
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        CustomerContext $customerContext,
        AddressRegistry $addressRegistry,
        CustomerAddressRepositoryInterface $addressRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->addressRegistry = $addressRegistry;
        $this->addressRepository = $addressRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->customerContext = $customerContext;
    }

    /**
     * @param SearchCriteriaInterface|null $searchCriteria
     * @return AddressSearchResultsInterface
     * @throws AuthorizationException
     * @throws LocalizedException
     */
    public function getCustomerAddressList(SearchCriteriaInterface $searchCriteria = null)
    {
        $this->customerContext->checkCustomerContext();

        $searchCriteriaBuilder = $this->searchCriteriaBuilder;
        if ($searchCriteria) {
            $searchCriteriaBuilder->setCurrentPage($searchCriteria->getCurrentPage());
            $searchCriteriaBuilder->setPageSize($searchCriteria->getPageSize());
            $searchCriteriaBuilder->setFilterGroups($searchCriteria->getFilterGroups());
            $searchCriteriaBuilder->setSortOrders($searchCriteria->getSortOrders() ?: []);
        }
        $customerId = $this->customerContext->getCurrentCustomerId();
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
        $this->customerContext->checkCustomerContext();
        $addressModel = $this->addressRegistry->retrieve($addressId);
        if ((int)$addressModel->getCustomerId() !== (int)$this->customerContext->getCurrentCustomerId()) {
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
        $this->customerContext->checkCustomerContext();
        $address->setId(null);
        $address->setCustomerId($this->customerContext->getCurrentCustomerId());
        return $this->ensureDefaultAddressFlags($this->addressRepository->save($address));
    }

    /**
     * @param AddressInterface $address
     * @return AddressInterface
     * @throws LocalizedException
     */
    public function updateCustomerAddress(AddressInterface $address)
    {
        $this->customerContext->checkCustomerContext();
        if (!$address->getId()) {
            throw new InputException(__('Provided address does not exists'));
        }
        $customerId = $this->customerContext->getCurrentCustomerId();
        $addressModel = $this->addressRegistry->retrieve($address->getId());
        if ((int)$addressModel->getCustomerId() !== (int)$customerId) {
            throw new AuthorizationException(__('Customer is not allowed to update this address'));
        }

        $address->setCustomerId($customerId);
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
        $this->customerContext->checkCustomerContext();
        /** @var Address $address */
        $address = $this->addressRegistry->retrieve($addressId);
        if ((int)$address->getCustomerId() !== (int)$this->customerContext->getCurrentCustomerId()) {
            throw new AuthorizationException(__('Customer is not allowed to delete this address'));
        }

        return $this->addressRepository->deleteById($addressId);
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