<?php
namespace Deity\MagentoApi\Api\Payment\Data;

/**
 * Class PaymentDataInterface
 * @package Deity\MagentoApi\Api\Data
 */
interface PaypalDataInterface
{
    /**
     * Token
     * @return string
     */
    public function getToken();

    /**
     * Sets token
     * @param $token
     * @return PaypalDataInterface
     */
    public function setToken($token);

    /**
     * Redirect url
     * @return string
     */
    public function getUrl();

    /**
     * Sets url
     * @param string $url
     * @return PaypalDataInterface
     */
    public function setUrl($url);

    /**
     * Sets error
     * @param string $error
     * @return string
     */
    public function setError($error);

    /**
     * Error
     * @return string
     */
    public function getError();
}