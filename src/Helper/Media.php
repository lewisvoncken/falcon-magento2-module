<?php

namespace Deity\MagentoApi\Helper;

use Magento\Catalog\Model\Product as MagentoProduct;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class Media extends AbstractHelper
{
    /** @var Image */
    protected $imageHelper;

    public function __construct(
        Context $context,
        Image $image
    ) {
        parent::__construct($context);
        $this->imageHelper = $image;
    }

    /**
     * @param MagentoProduct $product
     * @param string $imageFile
     * @param string $size
     * @return string
     */
    public function getProductImageUrl(MagentoProduct $product, $imageFile, $size)
    {
        return $this->imageHelper->getProductImageUrl($product, $imageFile, $size);
    }

    /**
     * @param MagentoProduct $product
     * @param string $size
     * @return string
     */
    public function getMainProductImageUrl(MagentoProduct $product, $size)
    {
        return $this->imageHelper->getMainProductImageUrl($product, $size);
    }

    /**
     * @return string
     */
    public function getBaseMediaUrl()
    {
        return $this->imageHelper->getBaseMediaUrl();
    }

    /**
     * @param MagentoProduct $product
     * @param int $mediaId
     * @return string
     */
    public function getProductVideoUrl(MagentoProduct $product, $mediaId)
    {
        $mediaGallery = $product->getMediaGallery();
        if (
            isset ($mediaGallery['images']) &&
            isset ($mediaGallery['images'][$mediaId]) &&
            isset ($mediaGallery['images'][$mediaId]['video_url'])
        ) {
            return $mediaGallery['images'][$mediaId]['video_url'];
        }

        return null;
    }
}