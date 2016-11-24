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
        // Changing "priceCalculation" policy to return a base price for configurable product
        $product->setPriceCalculation(false);

        $this->productHelper->addProductImageAttribute($product);
        $this->productHelper->addProductImageAttribute($product, 'product_list_image', 'thumbnail_url');
        $this->productHelper->addMediaGallerySizes($product);

        return $product;
    }
}
