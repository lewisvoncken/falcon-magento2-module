<?php

namespace Deity\MagentoApi\Plugin\Catalog\Model;

use Deity\MagentoApi\Helper\Breadcrumb;
use Deity\MagentoApi\Helper\Product as DeityProductHelper;
use Magento\Catalog\Model\Product as MagentoProduct;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * @package Deity\MagentoApi\Model\Plugin
 */
class Product
{
    /** @var DeityProductHelper */
    protected $productHelper;

    /** @var Breadcrumb */
    protected $breadcrumbHelper;

    /** @var ScopeConfigInterface */
    protected $scopeConfig;

    /**
     * @param DeityProductHelper $productHelper
     * @param Breadcrumb $breadcrumbHelper
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        DeityProductHelper $productHelper,
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
     * @throws LocalizedException
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
