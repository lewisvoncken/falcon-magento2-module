<?php

namespace Hatimeria\Reagento\Helper;

use Magento\Catalog\Api\Data\ProductExtension;
use Magento\Catalog\Api\Data\ProductExtensionFactory;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product as MagentoProduct;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context as AppContext;

/**
 * @package Hatimeria\Reagento\Helper
 */
class Product extends AbstractHelper
{
    /** @var ImageHelper */
    private $imageHelper;

    /** @var ProductExtensionFactory */
    private $productExtensionFactory;

    /**
     * @param AppContext $context
     * @param ProductExtensionFactory $productExtensionFactory
     * @param ImageHelper $imageHelper
     */
    public function __construct(
        AppContext $context,
        ProductExtensionFactory $productExtensionFactory,
        ImageHelper $imageHelper
    ) {
        parent::__construct($context);
        $this->productExtensionFactory = $productExtensionFactory;
        $this->imageHelper = $imageHelper;
    }

    /**
     * @param MagentoProduct $product
     * @param string $size
     * @param string $attributeName
     */
    public function addProductImageAttribute($product, $size = 'product_list_thumbnail', $attributeName = 'thumbnail_resized_url')
    {
        $productExtension = $this->getProductExtensionAttributes($product);
        $helper = $this->imageHelper->init($product, $size);
        $url = $helper
            ->setImageFile($product->getImage())
            ->getUrl();

        $productExtension->setData($attributeName, $url);
        $product->setExtensionAttributes($productExtension);
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