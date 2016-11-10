<?php

namespace Hatimeria\Reagento\Model;

use Magento\Framework\Model\AbstractModel;
use Hatimeria\Reagento\Api\Data\GalleryMediaEntrySizeInterface;

class GalleryMediaEntrySize extends AbstractModel implements GalleryMediaEntrySizeInterface
{
    const FULL = 'full';
    const THUMBNAIL = 'thumbnail';

    /**
     * @return string
     */
    public function getFull()
    {
        return $this->getData(self::FULL);
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setFull($url)
    {
        return $this->setData(self::FULL, $url);
    }

    /**
     * @return string
     */
    public function getThumbnail()
    {
        return $this->getData(self::THUMBNAIL);
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setThumbnail($url)
    {
        return $this->setData(self::THUMBNAIL, $url);
    }
}