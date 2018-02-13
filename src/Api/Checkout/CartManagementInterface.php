<?php

namespace Deity\MagentoApi\Api\Checkout;

use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Api\CartManagementInterface as MagentoCartManagementInterface;

/**
 * Interface CartManagementInterface
 * @api
 */
interface CartManagementInterface extends MagentoCartManagementInterface
{
    /**
     * Places an order for a specified cart.
     *
     * @param int $cartId The cart ID.
     * @param PaymentInterface|null $paymentMethod
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return \Deity\MagentoApi\Api\Data\OrderResponseInterface
     */
    public function placeOrder($cartId, PaymentInterface $paymentMethod = null);
}
