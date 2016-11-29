<?php

namespace Hatimeria\Reagento\Helper;

use Hatimeria\Reagento\Api\Data\GalleryMediaEntrySizeInterface;
use Hatimeria\Reagento\Helper\Image as ImageHelper;
use Magento\Catalog\Api\Data\ProductExtension;
use Magento\Catalog\Api\Data\ProductExtensionFactory;
use Magento\Catalog\Model\Product as MagentoProduct;
use Magento\Catalog\Model\Product\Gallery\ReadHandler as GalleryReadHandler;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context as AppContext;
use Magento\Framework\ObjectManagerInterface;

/**
 * @package Hatimeria\Reagento\Helper
 */
class Product extends AbstractHelper
{
    /** @var ImageHelper */
    private $imageHelper;

    /** @var ProductExtensionFactory */
    private $productExtensionFactory;

    /** @var ObjectManagerInterface */
    protected $objectManager;

    /** @var GalleryReadHandler */
    protected $galleryReadHandler;

    /**
     * @param AppContext $context
     * @param ProductExtensionFactory $productExtensionFactory
     * @param ImageHelper $imageHelper
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        AppContext $context,
        ProductExtensionFactory $productExtensionFactory,
        ImageHelper $imageHelper,
        GalleryReadHandler $galleryReadHandler,
        ObjectManagerInterface $objectManager
    ) {
        parent::__construct($context);
        $this->productExtensionFactory = $productExtensionFactory;
        $this->objectManager = $objectManager;
        $this->imageHelper = $imageHelper;
        $this->galleryReadHandler = $galleryReadHandler;
    }

    /**
     * @param MagentoProduct $product
     * @param string $size
     * @param string $attributeName
     */
    public function addProductImageAttribute($product, $size = 'product_list_thumbnail', $attributeName = 'thumbnail_resized_url')
    {
        $productExtension = $this->getProductExtensionAttributes($product);
        $productExtension->setData($attributeName, $this->imageHelper->getMainProductImageUrl($product, $size));
        $product->setExtensionAttributes($productExtension);
    }

    /**
     * @param MagentoProduct $product
     */
    public function addMediaGallerySizes($product)
    {
        $this->galleryReadHandler->execute($product);

        $sizes = [];
        $mediaGalleryEntries = $product->getMediaGalleryEntries();
        if(!$mediaGalleryEntries) {
            return;
        }

        $extAttrs = $this->getProductExtensionAttributes($product);

        foreach ($mediaGalleryEntries as $mediaGalleryEntry) {
            if($mediaGalleryEntry->getMediaType() !== 'image' || $mediaGalleryEntry->isDisabled()) {
                continue;
            }

            /** @var GalleryMediaEntrySizeInterface $sizesEntry */
            $sizesEntry = $this->objectManager->create('Hatimeria\Reagento\Api\Data\GalleryMediaEntrySizeInterface');

            $file = $mediaGalleryEntry->getFile();
            $sizesEntry->setFull($this->imageHelper->getProductImageUrl($product, $file, 'product_media_gallery_item'));
            $sizesEntry->setThumbnail($this->imageHelper->getProductImageUrl($product, $file, 'product_media_gallery_item_thumbnail'));
            $sizes[] = $sizesEntry;
        }

        $extAttrs->setMediaGallerySizes($sizes);
        $product->setExtensionAttributes($extAttrs);
    }

    /**
     * Changing "priceCalculation" policy to return a base price for configurable product
     * @param MagentoProduct $product
     */
    public function ensurePriceForConfigurableProduct($product)
    {
        if($product->getTypeId() === 'configurable') {
            $product->setPriceCalculation(false);
        }
    }

    /**
     * @param MagentoProduct $product
     * @return ProductExtension
     */
    protected function getProductExtensionAttributes($product)
    {
        $productExtension = $product->getExtensionAttributes();
        if ($productExtension === null) {
            $productExtension = $this->productExtensionFactory->create();
        }

        return $productExtension;
    }
}