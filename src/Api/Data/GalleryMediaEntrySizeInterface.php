<?php

namespace Hatimeria\Reagento\Api\Data;

interface GalleryMediaEntrySizeInterface
{
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

}