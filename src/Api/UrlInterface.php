<?php

namespace Deity\MagentoApi\Api;

/**
 * @package Deity\MagentoApi\Api
 */
interface UrlInterface
{
    /**
     * @return \Deity\MagentoApi\Api\Data\UrlDataInterface
     * @param string $requestPath
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getUrl($requestPath);
}
