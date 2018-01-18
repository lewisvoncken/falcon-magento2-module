<?php
namespace Hatimeria\Reagento\Model\Sales\OrderItem;

use Magento\Catalog\Api\Data\ProductExtensionInterface;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Api\Data\OrderItemExtensionInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\Order\Item;

class Extension
{
    /** @var ExtensionAttributesFactory */
    protected $extensionAttributesFactory;

    /** @var PriceCurrencyInterface */
    protected $priceCurrency;

    public function __construct(
        ExtensionAttributesFactory $extensionAttributesFactory,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->extensionAttributesFactory = $extensionAttributesFactory;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * Add extension attributes to order item extension
     *
     * @param Item|OrderItemInterface $item
     */
    public function addAttributes(OrderItemInterface $item)
    {
        $product = $item->getProduct();
        /** @var ProductExtensionInterface $productAttributes */
        $productAttributes = $product->getExtensionAttributes();
        $extensionAttributes = $this->getOrderItemExtensionAttribute($item);
        $extensionAttributes->setThumbnailUrl($productAttributes->getThumbnailUrl());
        $extensionAttributes->setUrlKey($product->getUrlKey());
        $extensionAttributes->setLink($product->getProductUrl());
        $extensionAttributes->setDisplayPrice($this->priceCurrency->format($productAttributes->getCatalogDisplayPrice(), false));
        $extensionAttributes->setRowTotalInclTax($this->priceCurrency->format($item->getRowTotalInclTax(), false));
        $item->setExtensionAttributes($extensionAttributes);
    }

    /**
     * @param OrderItemInterface $item
     * @return OrderItemExtensionInterface
     */
    protected function getOrderItemExtensionAttribute(OrderItemInterface $item)
    {
        $extensionAttributes = $item->getExtensionAttributes();
        if (!$extensionAttributes) {
            $extensionAttributes = $this->extensionAttributesFactory->create(OrderItemInterface::class);
        }

        return $extensionAttributes;
    }
}