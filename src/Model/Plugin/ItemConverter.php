<?php

namespace Hatimeria\Reagento\Model\Plugin;

class ItemConverter
{
    /** @var \Magento\Quote\Api\Data\TotalsItemExtensionFactory */
    protected $factory;

    /**
     * @param \Magento\Quote\Api\Data\TotalsItemExtensionFactory $factory
     */
    public function __construct(
        \Magento\Quote\Api\Data\TotalsItemExtensionFactory $factory
    ) {
        $this->factory = $factory;
    }

    /**
     * @param \Magento\Quote\Model\Cart\Totals\ItemConverter $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return \Magento\Quote\Model\Cart\Totals\Item
     */
    public function aroundModelToDataObject(
        \Magento\Quote\Model\Cart\Totals\ItemConverter $subject,
        \Closure $proceed,
        $item
    ) {
        $result = $proceed($item);

        $thumbnail = null;

        $product = $item->getProduct();
        if ($productExtensionAttributes = $product->getExtensionAttributes()) {
            $thumbnailUrl = $productExtensionAttributes->getThumbnailUrl();
        }

        $extensionAttributes = $this->factory->create(['data' => ['thumbnail_url' => $thumbnailUrl, 'url_key' => $product->getUrlKey()]]);
        $result->setExtensionAttributes($extensionAttributes);

        return $result;
    }
}
