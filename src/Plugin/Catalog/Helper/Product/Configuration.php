<?php

namespace Deity\MagentoApi\Plugin\Catalog\Helper\Product;

use Magento\Catalog\Helper\Product\Configuration as CatalogHelper;

class Configuration
{
    /**
     * @param CatalogHelper $subject
     * @param callable $proceed
     * @param string|array $optionValue
     * @param array $params
     * @return mixed
     */
    public function aroundGetFormattedOptionValue(CatalogHelper $subject, callable $proceed, $optionValue, $params = null)
    {
        $result = $proceed($optionValue, $params);
        if (array_key_exists('data', $optionValue)) {
            $result['data'] = $optionValue['data'];
        }
        return $result;
    }
}