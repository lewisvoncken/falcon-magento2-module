<?php

namespace Deity\MagentoApi\Model;

use Magento\Framework\Model\AbstractModel;
use Deity\MagentoApi\Api\Data\GalleryMediaEntrySizeInterface;

class GalleryMediaEntrySize extends AbstractModel implements GalleryMediaEntrySizeInterface
{
    const TYPE = 'type';
    const FULL = 'full';
    const THUMBNAIL = 'thumbnail';
    const EMBED_URL = 'embed_url';

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

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @param string $type
     * @return self
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * @param string $url
     * @return self
     */
    public function setEmbedUrl($url)
    {
        return $this->setData(self::EMBED_URL, $url);
    }

    /**
     * @return string
     */
    public function getEmbedUrl()
    {
        return $this->getData(self::EMBED_URL);
    }
}