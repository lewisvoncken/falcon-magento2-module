<?php

namespace Hatimeria\Reagento\Helper;

use Magento\Catalog\Model\Product as MagentoProduct;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Tax\Api\TaxCalculationInterface;

class Price extends AbstractHelper
{
    /** @var TaxCalculationInterface */
    protected $taxCalculation;

    /** @var array  */
    protected $rates = [];

    /** @var array  */
    protected $priceConfig = [];

    /**
     * Price constructor.
     * @param Context $context
     * @param TaxCalculationInterface $taxCalculation
     */
    public function __construct(
        Context $context,
        TaxCalculationInterface $taxCalculation
    )
    {
        parent::__construct($context);
        $this->taxCalculation = $taxCalculation;
    }

    /**
     * Calculate display price for product
     *
     * @param MagentoProduct $product
     * @return int|null
     */
    public function calculateCatalogDisplayPrice(MagentoProduct $product)
    {
        $productRateId = $product->getData('tax_class_id');
        if ($productRateId) {
            // First get base price (=price excluding tax)
            $rate = $this->getRate($productRateId);

            // Product price in catalog is including tax.
            $basePriceInclTax = $this->getConfig('tax/calculation/price_includes_tax', $product->getStoreId()) === 1;

            if ($basePriceInclTax) {
                $priceExcludingTax = $product->getPrice() / (1 + ($rate / 100));
            } else {
                // Product price in catalog is excluding tax.
                $priceExcludingTax = $product->getPrice();
            }

            $priceIncludingTax = round($priceExcludingTax + ($priceExcludingTax * ($rate / 100)), 2);

            // 2 - display prices including tax
            $catalogPriceInclTax = $this->getConfig('tax/display/type', $product->getStoreId()) === 2;

            return $catalogPriceInclTax ? $priceIncludingTax : $priceExcludingTax;
        }

        return null;
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