<?php

namespace Deity\MagentoApi\Helper;

use Magento\Catalog\Model\Product as MagentoProduct;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Tax\Api\TaxCalculationInterface;

class Price extends AbstractHelper
{
    /** @var TaxCalculationInterface */
    protected $taxCalculation;

    /** @var array */
    protected $rates = [];

    /** @var array */
    protected $priceConfig = [];

    /**
     * Price constructor.
     * @param Context $context
     * @param TaxCalculationInterface $taxCalculation
     */
    public function __construct(
        Context $context,
        TaxCalculationInterface $taxCalculation
    ) {
        parent::__construct($context);
        $this->taxCalculation = $taxCalculation;
    }

    /**
     * Calculate display price for product
     *
     * @param MagentoProduct $product
     * @return array
     */
    public function calculateCatalogDisplayPrice(MagentoProduct $product)
    {
        $productRateId = $product->getData('tax_class_id');
        if ($productRateId !== null) {

            // First get base price (=price excluding tax)
            $rate = $this->getRate($productRateId);

            return [
                'calculated_price' => (float)$this->getDisplayPrice($product->getPrice(), $product->getStoreId(), $rate),
                'min_price' => (float)$this->getDisplayPrice($product->getMinimalPrice(), $product->getStoreId(), $rate),
                'max_price' => (float)$this->getDisplayPrice($product->getMaxPrice(), $product->getStoreId(), $rate)
            ];
        }

        return null;
    }

    /**
     * Get display price value
     *
     * @param float $price
     * @param int $storeId
     * @param mixed $rate
     * @return float|int
     */
    protected function getDisplayPrice($price, $storeId, $rate)
    {
        // Product price in catalog is including tax.
        $basePriceInclTax = $this->getConfig('tax/calculation/price_includes_tax', $storeId) === 1;

        if ($basePriceInclTax) {
            $priceExcludingTax = $price / (1 + ($rate / 100));
        } else {
            // Product price in catalog is excluding tax.
            $priceExcludingTax = $price;
        }

        $priceIncludingTax = round($priceExcludingTax + ($priceExcludingTax * ($rate / 100)), 2);

        // 2 - display prices including tax
        $catalogPriceInclTax = $this->getConfig('tax/display/type', $storeId) === 2;

        return $catalogPriceInclTax ? $priceIncludingTax : $priceExcludingTax;
    }

    /**
     * Get tax rate value
     *
     * @param int $productRateId
     * @return mixed
     */
    protected function getRate($productRateId)
    {
        if (!array_key_exists($productRateId, $this->rates)) {
            $this->rates[$productRateId] = $this->taxCalculation->getCalculatedRate($productRateId);
        }

        return $this->rates[$productRateId];
    }

    /**
     * Load config value or take cached one
     *
     * @param string $path
     * @param int $storeId
     * @return mixed
     */
    protected function getConfig($path, $storeId)
    {
        if (
            !array_key_exists($path, $this->priceConfig)
            || !array_key_exists($storeId, $this->priceConfig[$path])
        ) {
            $value = (int) $this->scopeConfig->getValue(
                $path,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
            if (!array_key_exists($path, $this->priceConfig)) {
                $this->priceConfig[$path] = [];
            }
            $this->priceConfig[$path][$storeId] = $value;
        }

        return $this->priceConfig[$path][$storeId];
    }

}