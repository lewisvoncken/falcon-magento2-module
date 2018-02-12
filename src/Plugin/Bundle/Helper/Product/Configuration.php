<?php

namespace Deity\MagentoApi\Plugin\Bundle\Helper\Product;

use Magento\Bundle\Helper\Catalog\Product\Configuration as BundleHelper;

class Configuration
{
    /**
     * @param BundleHelper $subject
     * @param array $result
     * @return array
     */
    public function afterGetBundleOptions(BundleHelper $subject, $result)
    {
        foreach($result as $optionId => $option) {
            $result[$optionId]['data'] = [];
            foreach($option['value'] as $valueId => $value) {
                $info = [];
                //not great solution but much quicker than parsing bundle option once more
                preg_match('/^([0-9]+) x (.+) <span.+>(.+)<\/span>/', $value, $info);
                $result[$optionId]['data'][$valueId] = [
                    'qty' => $info[1],
                    'name' => $info[2],
                    'price' => $info[3]
                ];
            }
        }
        return $result;
    }
}