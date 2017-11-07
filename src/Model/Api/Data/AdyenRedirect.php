<?php
namespace Hatimeria\Reagento\Model\Api\Data;

use Hatimeria\Reagento\Api\Data\AdyenRedirectInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class AdyenRedirect extends AbstractExtensibleModel implements AdyenRedirectInterface
{

    /**
     * @param $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setIssuerUrl($param)
    {
        return $this->setData(self::ISSUER_URL, $param);
    }

    /**
     * @return string
     */
    public function getIssuerUrl()
    {
        return $this->_getData(self::ISSUER_URL);
    }

    /**
     * @param $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setMd($param)
    {
        return $this->setData(self::MD, $param);
    }

    /**
     * @return string
     */
    public function getMd()
    {
        return $this->_getData(self::MD);
    }

    /**
     * @param $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setPaRequest($param)
    {
        return $this->setData(self::PA_REQUEST, $param);
    }

    /**
     * @return string
     */
    public function getPaRequest()
    {
        return $this->_getData(self::PA_REQUEST);
    }

    /**
     * @param $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setTermUrl($param)
    {
        return $this->setData(self::TERM_URL, $param);
    }

    /**
     * @return string
     */
    public function getTermUrl()
    {
        return $this->_getData(self::TERM_URL);
    }
}