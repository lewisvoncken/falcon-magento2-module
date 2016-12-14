<?php

namespace Hatimeria\Reagento\Api;

/**
 * @package Hatimeria\Reagento\Api
 */
interface UrlInterface
{
    /**
     * @return \Hatimeria\Reagento\Api\Data\UrlDataInterface
     * @param string $requestPath
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getUrl($requestPath);
}
