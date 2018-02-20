<?php

namespace Deity\MagentoApi\Model\Payment;

use Magento\Framework\Model\AbstractModel;
use Deity\MagentoApi\Api\Payment\Data\PaypalDataInterface;

/**
 * Class PaymentData
 * @package Deity\MagentoApi\Model
 */
class PaypalData extends AbstractModel implements PaypalDataInterface
{
    /**
     * Set Token
     * @param $token
     * @return $this
     */
    public function setToken($token)
    {
        return $this->setData('token', $token);
    }

    /**
     * Token
     * @return string
     */
    public function getToken()
    {
        return $this->_getData('token');
    }

    /**
     * Sets url
     * @param string $url
     * @return PaymentDataInterface
     */
    public function setUrl($url)
    {
        return $this->setData('url', $url);
    }

    /**
     * Redirect url
     * @return string
     */
    public function getUrl()
    {
        return $this->_getData('url');
    }

    /**
     * Sets error
     * @param string $error
     * @return string
     */
    public function setError($error)
    {
        return $this->setData('error', $error);
    }

    /**
     * Error
     * @return string
     */
    public function getError()
    {
        return $this->_getData('error');
    }
}