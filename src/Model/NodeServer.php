<?php

namespace Hatimeria\Reagento\Model;

use Hatimeria\Reagento\Helper\Data as ReagentoHelper;

/**
 * Service to communicate with node internal api endpoints
 */
class NodeServer
{
    /**
     * Curl Adapter Factory
     *
     * @var \Magento\Framework\HTTP\Adapter\CurlFactory
     */
    protected $curlFactory;
    /**
     * Basic reagento helper
     *
     * @var ReagentoHelper
     */
    protected $reagentoHelper;

    /**
     * @param \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory
     * @param ReagentoHelper $reagentoHelper
     */
    public function __construct(
        \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory,
        ReagentoHelper $reagentoHelper
    )
    {
        $this->curlFactory    = $curlFactory;
        $this->reagentoHelper = $reagentoHelper;
    }

    /**
     * Send invalidation to reagento node app
     *
     * @param [] $tags list of tags to invalidate
     */
    public function sendInvalidate($tags)
    {
        $curl = $this->curlFactory->create();
        $curl->setConfig(
            [
                'timeout'   => 2,
            ]
        );

        $params = [
            'tags' => implode(',', $tags),
        ];
        $url = rtrim($this->reagentoHelper->getNodeServerUrl(), '/'); 
        $url .= '/api/cache/invalidate?';
        $url .= http_build_query($params);
        $curl->write(\Zend_Http_Client::GET, $url, '1.0');
        $data = $curl->read();

        if ($data === false) {
            return false;
        }
        $data = preg_split('/^\r?$/m', $data, 2);
        $data = trim($data[1]);
        $curl->close();

        return !empty($data);
    }

}
