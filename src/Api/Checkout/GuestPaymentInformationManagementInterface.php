<?php

namespace Deity\MagentoApi\Api\Checkout;

use Magento\Checkout\Api\GuestPaymentInformationManagementInterface as MagentoGuestPaymentInformationManagementInterface;

/**
 * Interface for managing guest payment information
 * @api
 */
interface GuestPaymentInformationManagementInterface extends MagentoGuestPaymentInformationManagementInterface
{
    /**
     * Set payment information and place order for a specified cart.
     *
     * @param string $cartId
     * @param string $email
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return int | \Deity\MagentoApi\Api\Data\OrderResponseInterface
     */
    public function savePaymentInformationAndPlaceOrder(
        $cartId,
        $email,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    );
}
