<?php

namespace Deity\MagentoApi\Model;

use Deity\MagentoApi\Helper\Data as DeityHelper;
use Magento\Framework\HTTP\Adapter\CurlFactory;

/**
 * Service to communicate with node internal api endpoints
 */
class NodeServer
{
    /**
     * Curl Adapter Factory
     *
     * @var CurlFactory
     */
    protected $curlFactory;
    /**
     * Basic deity helper
     *
     * @var DeityHelper
     */
    protected $deityHelper;

    /**
     * @param CurlFactory $curlFactory
     * @param DeityHelper $deityHelper
     */
    public function __construct(
        CurlFactory $curlFactory,
        DeityHelper $deityHelper
    )
    {
        $this->curlFactory    = $curlFactory;
        $this->deityHelper = $deityHelper;
    }

    /**
     * Send invalidation to deity node app
     *
     * @param [] $tags list of tags to invalidate
     * @return bool
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
        $url = rtrim($this->deityHelper->getNodeServerUrl(), '/');
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
