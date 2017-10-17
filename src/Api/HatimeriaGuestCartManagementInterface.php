<?php

namespace Hatimeria\Reagento\Api;

use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Api\GuestCartManagementInterface;

/**
 * Interface HatimeriaCartManagementInterface
 * @api
 */
interface HatimeriaGuestCartManagementInterface extends GuestCartManagementInterface
{
    /**
     * Places an order for a specified cart.
     *
     * @param string $cartId The cart ID.
     * @param PaymentInterface|null $paymentMethod
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return \Hatimeria\Reagento\Model\Api\OrderResponse
     */
    public function placeOrder($cartId, PaymentInterface $paymentMethod = null);
}
