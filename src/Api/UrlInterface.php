<?php

namespace Hatimeria\Reagento\Api;

/**
 * @package Hatimeria\Reagento\Api
 */
interface UrlInterface
{
    /**
     * @return \Magento\Cms\Api\Data\PageInterface
     * @param string $url
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getUrl($url);
}
