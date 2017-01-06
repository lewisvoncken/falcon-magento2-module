<?php
namespace Hatimeria\Reagento\Api\Payment;

/**
 * Interface PaymentInterface
 * @package Hatimeria\Reagento\Api
 */
interface PaypalInterface
{
    /**
     * Fetch PayPal token
     * @param string $cartId
     * @return \Hatimeria\Reagento\Api\Payment\Data\PaypalDataInterface
     */
    public function getToken($cartId);
}