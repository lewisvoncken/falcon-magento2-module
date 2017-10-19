<?php

namespace Hatimeria\Reagento\Api;

/**
 * Interface MenuRepositoryInterface
 * @package Hatimeria\Reagento\Api
 * @api
 */
interface MenuRepositoryInterface
{
    /**
     * @return \Hatimeria\Reagento\Api\Data\MenuInterface
     */
    public function getTree();
}