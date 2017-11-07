<?php

namespace Hatimeria\Reagento\Api;

use Magento\Checkout\Api\PaymentInformationManagementInterface;

/**
 * Interface for managing guest payment information
 * @api
 */
interface HatimeriaPaymentInformationManagementInterface extends PaymentInformationManagementInterface
{
    /**
     * Set payment information and place order for a specified cart.
     *
     * @param int $cartId
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return \Hatimeria\Reagento\Api\Data\OrderResponseInterface | int
     */
    public function savePaymentInformationAndPlaceOrder(
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    );
}
