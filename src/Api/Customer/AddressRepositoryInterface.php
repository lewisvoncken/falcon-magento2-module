<?php
namespace Hatimeria\Reagento\Api\Customer;

use Magento\Customer\Api\Data\AddressInterface;

interface AddressRepositoryInterface
{
    /**
     * @param \Magento\Customer\Api\Data\AddressInterface $address
     * @return \Magento\Customer\Api\Data\AddressInterface
     */
    public function createCustomerAddress(AddressInterface $address);

    /**
     * @param \Magento\Customer\Api\Data\AddressInterface $address
     * @return \Magento\Customer\Api\Data\AddressInterface
     */
    public function updateCustomerAddress(AddressInterface $address);

    /**
     * @param int $addressId
     * @return bool
     */
    public function deleteCustomerAddress($addressId);
}