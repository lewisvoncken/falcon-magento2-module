<?php

namespace Hatimeria\Reagento\Api\Payment;

interface AdyenLinkInterface
{
    /**
     * Get adyen payment link for the given order
     *
     * @param int $orderId
     * @param int $customerId
     * @return string
     */
    public function getCustomerOrderPaymentLink($orderId, $customerId);

    /**
     * @param int $orderId
     * @param string $cartId
     * @return mixed
     */
    public function getGuestOrderPaymentLink($orderId, $cartId);
}