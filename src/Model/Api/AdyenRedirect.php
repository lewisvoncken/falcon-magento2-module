<?php
namespace Hatimeria\Reagento\Model\Api;

use Magento\Framework\DataObject;

class AdyenRedirect extends DataObject
{
    /**
     * @return string
     */
    public function getIssuerUrl()
    {
        return $this->_data['issuer_url'];
    }

    /**
     * @return string
     */
    public function getMd()
    {
        return $this->_data['md'];
    }

    /**
     * @return string
     */
    public function getPaRequest()
    {
        return $this->_data['pa_request'];
    }


    /**
     * @return string
     */
    public function getTermUrl()
    {
        return $this->_data['term_url'];
    }

    /**
     * @param string $key
     * @param null $index
     * @return array
     */
    public function getData($key = '', $index = null)
    {
        return [];
    }
}