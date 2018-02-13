<?php

namespace Deity\MagentoApi\Api;

/**
 * Interface MenuRepositoryInterface
 * @package Deity\MagentoApi\Api
 * @api
 */
interface MenuRepositoryInterface
{
    /**
     * @return \Deity\MagentoApi\Api\Data\MenuInterface[]
     */
    public function getTree();
}