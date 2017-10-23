<?php

namespace Hatimeria\Reagento\Api\Data;

interface AdyenRedirectInterface
{
    const ISSUER_URL = 'issuer_url';
    const MD = 'md';
    const PA_REQUEST = 'pa_request';
    const TERM_URL = 'term_url';
    /**
     * @param $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setIssuerUrl($param);

    /**
     * @return string
     */
    public function getIssuerUrl();

    /**
     * @param $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setMd($param);

    /**
     * @return string
     */
    public function getMd();

    /**
     * @param $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setPaRequest($param);

    /**
     * @return string
     */
    public function getPaRequest();

    /**
     * @param $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setTermUrl($param);

    /**
     * @return string
     */
    public function getTermUrl();
}