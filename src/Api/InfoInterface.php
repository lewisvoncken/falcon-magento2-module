<?php

namespace Deity\MagentoApi\Api;

/**
 * @package Deity\MagentoApi\Api
 */
interface InfoInterface
{
    /**
     * @return \Deity\MagentoApi\Api\Data\InfoDataInterface
     */
    public function getInfo();
}