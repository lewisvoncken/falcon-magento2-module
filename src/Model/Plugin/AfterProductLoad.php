<?php

namespace Hatimeria\Reagento\Model\Plugin;

use Hatimeria\Reagento\Helper\Product as HatimeriaProductHelper;
use Magento\Catalog\Model\Product;

/**
 * @package Hatimeria\Reagento\Model\Plugin
 */
class AfterProductLoad
{
    /** @var HatimeriaProductHelper */
    private $productHelper;

    /**
     * @param HatimeriaProductHelper $productHelper
     */
    public function __construct(HatimeriaProductHelper $productHelper)
    {
        $this->productHelper = $productHelper;
    }

    /**
     * Add resized image information to the product's extension attributes.
     *
     * @param Product $product
     * @return Product
     */
    public function afterLoad(Product $product)
    {
        $this->productHelper->ensurePriceForConfigurableProduct($product);
        $this->productHelper->ensureOptionsForConfigurableProduct($product);

        $this->productHelper->addProductImageAttribute($product);
        $this->productHelper->addProductImageAttribute($product, 'product_list_image', 'thumbnail_url');
        $this->productHelper->addMediaGallerySizes($product);

        $this->productHelper->calculateCatalogDisplayPrice($product);

        return $product;
    }
}
