<?php
namespace Deity\MagentoApi\Api\Payment;

/**
 * Interface PaymentInterface
 * @package Deity\MagentoApi\Api
 */
interface PaypalInterface
{
    /**
     * Fetch PayPal token
     * @param string $cartId
     * @return \Deity\MagentoApi\Api\Payment\Data\PaypalDataInterface
     */
    public function getToken($cartId);
}