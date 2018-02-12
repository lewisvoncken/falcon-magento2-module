<?php

namespace Deity\MagentoApi\Plugin\Bundle\Product;

use Magento\Bundle\Api\Data\LinkInterface;
use Magento\Bundle\Api\Data\LinkExtensionInterface;
use Magento\Bundle\Model\Product\LinksList as BundleProductLinksList;
use Magento\Bundle\Model\Product\Type;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Api\ExtensionAttributesFactory;

class LinksList
{
    /** @var ExtensionAttributesFactory */
    private $extensionAttributesFactory;

    /** @var Type */
    private $type;

    /**
     * LinksList constructor.
     * @param ExtensionAttributesFactory $extensionAttributesFactory
     * @param Type $type
     */
    public function __construct(
        ExtensionAttributesFactory $extensionAttributesFactory,
        Type $type
    ) {
        $this->extensionAttributesFactory = $extensionAttributesFactory;
        $this->type = $type;
    }

    /**
     * Add extension attributes to bundle option product links
     *
     * @param BundleProductLinksList $subject
     * @param callable $proceed
     * @param ProductInterface $product
     * @param $optionId
     * @return mixed
     */
    public function aroundGetItems(BundleProductLinksList $subject, callable $proceed, ProductInterface $product, $optionId)
    {
        $productLinks = $proceed($product, $optionId);
        $selectionCollection = $this->type->getSelectionsCollection([$optionId], $product);

        foreach ($productLinks as $productLink) { /** @var LinkInterface $productLink */
            /** @var ProductInterface $product */
            $product = $selectionCollection->getItemById($productLink->getId());
            $extensionAttributes = $this->getExtensionAttributes($productLink);
            $extensionAttributes->setName($product->getName());
            $extensionAttributes->setCatalogDisplayPrice($this->getProductCatalogDisplayPrice($product));
            $productLink->setExtensionAttributes($extensionAttributes);
        }

        return $productLinks;
    }

    /**
     * Get or create link extension attribute class
     *
     * @param LinkInterface $productLink
     * @return LinkExtensionInterface
     */
    protected function getExtensionAttributes(LinkInterface $productLink)
    {
        $extensionAttributes = $productLink->getExtensionAttributes();
        if (!$extensionAttributes) {
            $extensionAttributes = $this->extensionAttributesFactory->create(LinkInterface::class);
        }

        return $extensionAttributes;
    }

    /**
     * Get price data for selection
     * @param ProductInterface $product
     * @return float|null
     */
    protected function getProductCatalogDisplayPrice(ProductInterface $product)
    {
        $extensionAttribute = $product->getExtensionAttribute();
        if ($extensionAttribute) {
            return $extensionAttribute->getCatalogDisplayPrice();
        }

        return $product->getPrice();
    }
}
