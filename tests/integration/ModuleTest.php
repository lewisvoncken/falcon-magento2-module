<?php

namespace Deity\MagentoApi;

use Magento\Framework\Component\ComponentRegistrar;
use PHPUnit\Framework\TestCase;
use Magento\TestFramework\ObjectManager;

class ModuleTest extends TestCase
{
    const MODULE_NAME = 'Deity_MagentoApi';
    /**
     * @var ObjectManager
     */
    private $objectManager;

    protected function setUp()
    {
        $this->objectManager = ObjectManager::getInstance();
    }

    public function testTheModuleIsRegistered()
    {
        $registrar = new ComponentRegistrar();
        $paths = $registrar->getPaths(ComponentRegistrar::MODULE);
        $this->assertArrayHasKey(self::MODULE_NAME, $paths, 'Module should be registered');
    }

}