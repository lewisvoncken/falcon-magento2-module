<?php

namespace Deity\MagentoApi\Plugin\CatalogUrlRewrite\Model\Product;

use Deity\MagentoApi\Helper\Data;
use Magento\Catalog\Model\Product;
use Magento\CatalogUrlRewrite\Model\ObjectRegistry;
use Magento\CatalogUrlRewrite\Model\ObjectRegistryFactory;
use Magento\CatalogUrlRewrite\Model\Product\CurrentUrlRewritesRegenerator as MagentoCurrentUrlRewritesRegenerator;

class CurrentUrlRewritesRegenerator
{
    /** @var Data */
    protected $helper;

    /** @var ObjectRegistryFactory */
    protected $objectRegistryFactory;

    /**
     * AnchorUrlRewriteGenerator constructor.
     * @param Data $helper
     * @param ObjectRegistryFactory $objectRegistryFactory
     */
    public function __construct(Data $helper, ObjectRegistryFactory $objectRegistryFactory)
    {
        $this->helper = $helper;
        $this->objectRegistryFactory = $objectRegistryFactory;
    }

    /**
     * Prevent generating category product urls
     *
     * @param MagentoCurrentUrlRewritesRegenerator $subject
     * @param callable $proceed
     * @param $storeId
     * @param Product $product
     * @param ObjectRegistry $productCategories
     * @return mixed
     */
    public function aroundGenerate(
        MagentoCurrentUrlRewritesRegenerator $subject,
        callable $proceed,
        $storeId,
        Product $product,
        ObjectRegistry $productCategories
    ) {
        if (!$this->helper->shouldGenerateProductUrls($storeId)) {
            $productCategories = $this->objectRegistryFactory->create(['entities' => []]);
        }

        return $proceed($storeId, $product, $productCategories);
    }

}