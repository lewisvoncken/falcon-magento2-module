<?php
namespace Deity\MagentoApi;

use Magento\Framework\Webapi;
use Magento\TestFramework\TestCase;

/**
 * Tests for info service.
 */
class InfoTest extends TestCase\WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/info';

    /**
     * Test GET \Deity\MagentoApi\Api\InfoInterface
     */
    public function testGet()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Webapi\Rest\Request::HTTP_METHOD_GET,
            ],
        ];

        $block = $this->_webApiCall($serviceInfo);
        $this->assertNotNull($block['customer_register_url']);
        $this->assertNotNull($block['customer_dashboard_url']);
    }

}
