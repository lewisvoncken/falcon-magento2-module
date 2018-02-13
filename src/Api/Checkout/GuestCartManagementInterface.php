<?php

namespace Deity\MagentoApi\Api\Checkout;

use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Api\GuestCartManagementInterface as MagentoGuestCartManagementInterface;

/**
 * Interface CartManagementInterface
 * @api
 */
interface GuestCartManagementInterface extends MagentoGuestCartManagementInterface
{
    /**
     * Places an order for a specified cart.
     *
     * @param string $cartId The cart ID.
     * @param PaymentInterface|null $paymentMethod
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return \Deity\MagentoApi\Api\Data\OrderResponseInterface
     */
    public function placeOrder($cartId, PaymentInterface $paymentMethod = null);
}
