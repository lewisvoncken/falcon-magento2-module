<?php

namespace Deity\MagentoApi\Plugin\Bundle\Product;

use Magento\Bundle\Model\Product\Type as BundleType;
use Magento\Bundle\Model\ResourceModel\Selection\Collection as ResourceSelectionCollection;
use Magento\Catalog\Model\Product;

class Type
{
    /**
     * @var ResourceSelectionCollection[]
     */
    protected $cachedSelectionCollection = [];

    /**
     * Cache selection collection for speed future fetching the same data
     *
     * @param BundleType $subject
     * @param callable $proceed
     * @param array $optionIds
     * @param Product $product
     * @return ResourceSelectionCollection
     */
    public function aroundGetSelectionsCollection(BundleType $subject, callable $proceed, $optionIds, $product)
    {
        $cacheKey = $this->getCacheKey($optionIds, $product);
        if (!array_key_exists($cacheKey, $this->cachedSelectionCollection)) {
            $result = $proceed($optionIds, $product);
            $this->cachedSelectionCollection[$cacheKey] = $result;
        }

        return $this->cachedSelectionCollection[$cacheKey];
    }

    /**
     * Generate cache key for the product
     *
     * @param array $optionIds
     * @param Product $product
     * @return string
     */
    private function getCacheKey($optionIds, Product $product)
    {
        return implode('_', $optionIds) . '_' . $product->getId();
    }
}