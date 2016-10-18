<?php

namespace Hatimeria\Reagento\Api;

/**
 * @package Hatimeria\Reagento\Api
 */
interface UrlInterface
{
    /**
     * @return \Magento\Cms\Api\Data\PageInterface
     * @param string
     */
    public function getUrl($url);
}
