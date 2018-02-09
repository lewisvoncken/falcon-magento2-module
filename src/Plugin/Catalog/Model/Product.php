<?php

namespace Hatimeria\Reagento\Plugin\Catalog\Model;

use Hatimeria\Reagento\Helper\Breadcrumb;
use Hatimeria\Reagento\Helper\Product as HatimeriaProductHelper;
use Hatimeria\Reagento\Model\Config\Source\BreadcrumbsAttribute;
use Magento\Catalog\Model\Product as MagentoProduct;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * @package Hatimeria\Reagento\Model\Plugin
 */
class Product
{
    /** @var HatimeriaProductHelper */
    protected $productHelper;

    /** @var Breadcrumb */
    protected $breadcrumbHelper;

    /** @var ScopeConfigInterface */
    protected $scopeConfig;

    /**
     * @param HatimeriaProductHelper $productHelper
     * @param Breadcrumb $breadcrumbHelper
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        HatimeriaProductHelper $productHelper,
        Breadcrumb $breadcrumbHelper,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->productHelper = $productHelper;
        $this->scopeConfig = $scopeConfig;
        $this->breadcrumbHelper = $breadcrumbHelper;
    }

    /**
     * Add resized image information to the product's extension attributes.
     *
     * @param MagentoProduct $product
     * @return MagentoProduct
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterLoad(MagentoProduct $product)
    {
        $this->productHelper->ensurePriceForConfigurableProduct($product);
        $this->productHelper->ensureOptionsForConfigurableProduct($product);

        $this->productHelper->addProductImageAttribute($product);
        $this->productHelper->addProductImageAttribute($product, 'product_list_image', 'thumbnail_url');
        $this->productHelper->addMediaGallerySizes($product);
        $this->breadcrumbHelper->addProductBreadcrumbsData($product, $this->productHelper->getFilterableAttributes());

        $this->productHelper->calculateCatalogDisplayPrice($product);

        return $product;
    }
}
