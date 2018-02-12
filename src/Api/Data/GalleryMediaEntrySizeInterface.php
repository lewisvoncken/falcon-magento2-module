<?php

namespace Deity\MagentoApi\Api\Data;

interface GalleryMediaEntrySizeInterface
{

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     * @return self
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getFull();

    /**
     * @param string $url
     * @return self
     */
    public function setFull($url);

    /**
     * @return string
     */
    public function getThumbnail();

    /**
     * @param string $url
     * @return self
     */
    public function setThumbnail($url);

    /**
     * @param string $url
     * @return self
     */
    public function setEmbedUrl($url);

    /**
     * @return string
     */
    public function getEmbedUrl();
}