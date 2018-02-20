<?php

namespace Deity\MagentoApi\Plugin\CatalogUrlRewrite\Model\Product;

use Deity\MagentoApi\Helper\Data;
use Magento\Catalog\Model\Product;
use Magento\CatalogUrlRewrite\Model\ObjectRegistry;
use Magento\CatalogUrlRewrite\Model\Product\CategoriesUrlRewriteGenerator as MagentoCategoriesUrlRewriteGenerator;

class CategoriesUrlRewriteGenerator
{
    /** @var Data */
    protected $helper;

    /**
     * AnchorUrlRewriteGenerator constructor.
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Prevent category product url generation
     *
     * @param MagentoCategoriesUrlRewriteGenerator $subject
     * @param callable $proceed
     * @param $storeId
     * @param Product $product
     * @param ObjectRegistry $productCategories
     * @return array
     */
    public function aroundGenerate(
        MagentoCategoriesUrlRewriteGenerator $subject,
        callable $proceed,
        $storeId,
        Product $product,
        ObjectRegistry $productCategories
    ) {
        if ($this->helper->shouldGenerateProductUrls($storeId)) {
            return $proceed($storeId, $product, $productCategories);
        }

        return [];
    }
}