<?php

namespace Deity\MagentoApi\Plugin\Quote\Model\Cart\Totals;

use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Quote\Api\Data\TotalsItemExtensionFactory;
use Magento\Quote\Model\Cart\Totals\ItemConverter as MagentoItemConverter;
use Magento\Quote\Model\Cart\Totals\Item as TotalsItem;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Deity\MagentoApi\Model\Cart\Item\AttributeList;

class ItemConverter
{
    /** @var TotalsItemExtensionFactory */
    protected $factory;

    /** @var StockRegistryInterface */
    protected $stockRegistry;

    /** @var AttributeList */
    protected $attributeList;

    /**
     * @param TotalsItemExtensionFactory $factory
     * @param StockRegistryInterface $stockRegistry
     * @param AttributeList $attributeList
     */
    public function __construct(
        TotalsItemExtensionFactory $factory,
        StockRegistryInterface $stockRegistry,
        AttributeList $attributeList
    ) {
        $this->factory = $factory;
        $this->stockRegistry = $stockRegistry;
        $this->attributeList = $attributeList;
    }

    /**
     * @param MagentoItemConverter $subject
     * @param \Closure $proceed
     * @param QuoteItem $item
     * @return TotalsItem
     */
    public function aroundModelToDataObject(
        MagentoItemConverter $subject,
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
