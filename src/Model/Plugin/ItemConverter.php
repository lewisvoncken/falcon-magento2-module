<?php

namespace Hatimeria\Reagento\Model\Plugin;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class ItemConverter
{
    /** @var \Magento\Quote\Api\Data\TotalsItemExtensionFactory */
    protected $factory;

    /** @var \Magento\CatalogInventory\Api\StockRegistryInterface */
    protected $stockRegistry;

    /** @var \Hatimeria\Reagento\Model\Cart\Item\AttributeList */
    protected $attributeList;

    /**
     * @param \Magento\Quote\Api\Data\TotalsItemExtensionFactory $factory
     */
    public function __construct(
        \Magento\Quote\Api\Data\TotalsItemExtensionFactory $factory,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Hatimeria\Reagento\Model\Cart\Item\AttributeList $attributeList
    ) {
        $this->factory = $factory;
        $this->stockRegistry = $stockRegistry;
        $this->attributeList = $attributeList;
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

        $urlKey = $product->getUrlKey();

        if ($product->getTypeId() == Configurable::TYPE_CODE) {
            $product = $item->getChildren()[0]->getProduct();
        }

        $stockItem = $this->stockRegistry->getStockItem($product->getId());

        $attributes = [];
        foreach ($this->attributeList->getAttributes() as $attribute) {
            $customAttribute = $product->getCustomAttribute($attribute);
            $value = $customAttribute ? $customAttribute->getValue() : $product->getData($attribute);
            $attributes[$attribute] = $value;
        }

        $extensionAttributes = $this->factory->create(
            [
                'data' => [
                    'thumbnail_url' => $thumbnailUrl,
                    'url_key'       => $urlKey,
                    'available_qty' => $stockItem->getQty()
                ] + $attributes
            ]
        );
        $result->setExtensionAttributes($extensionAttributes);

        return $result;
    }
}
