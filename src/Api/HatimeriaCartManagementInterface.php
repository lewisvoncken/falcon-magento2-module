<?php

namespace Hatimeria\Reagento\Api;

use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Api\CartManagementInterface;

/**
 * Interface HatimeriaCartManagementInterface
 * @api
 */
interface HatimeriaCartManagementInterface extends CartManagementInterface
{
    /**
     * Places an order for a specified cart.
     *
     * @param int $cartId The cart ID.
     * @param PaymentInterface|null $paymentMethod
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return \Hatimeria\Reagento\Api\Data\OrderResponseInterface
     */
    public function placeOrder($cartId, PaymentInterface $paymentMethod = null);
}
