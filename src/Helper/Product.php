<?php

namespace Hatimeria\Reagento\Helper;

use Hatimeria\Reagento\Api\Data\GalleryMediaEntrySizeInterface;
use Hatimeria\Reagento\Helper\Image as ImageHelper;
use Magento\Catalog\Api\Data\ProductExtension;
use Magento\Catalog\Api\Data\ProductExtensionFactory;
use Magento\Catalog\Model\Product as MagentoProduct;
use Magento\Catalog\Model\Product\Gallery\ReadHandler as GalleryReadHandler;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context as AppContext;
use Magento\Framework\ObjectManagerInterface;

/**
 * @package Hatimeria\Reagento\Helper
 */
class Product extends AbstractHelper
{
    /** @var ImageHelper */
    private $imageHelper;

    /** @var ProductExtensionFactory */
    private $productExtensionFactory;

    /** @var ObjectManagerInterface */
    protected $objectManager;

    /** @var GalleryReadHandler */
    protected $galleryReadHandler;

    /** @var \Magento\Eav\Model\Config */
    protected $eavConfig;

    /**
     * @param AppContext $context
     * @param ProductExtensionFactory $productExtensionFactory
     * @param ImageHelper $imageHelper
     * @param ObjectManagerInterface $objectManager
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        AppContext $context,
        ProductExtensionFactory $productExtensionFactory,
        ImageHelper $imageHelper,
        GalleryReadHandler $galleryReadHandler,
        ObjectManagerInterface $objectManager,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        parent::__construct($context);
        $this->productExtensionFactory = $productExtensionFactory;
        $this->objectManager = $objectManager;
        $this->imageHelper = $imageHelper;
        $this->galleryReadHandler = $galleryReadHandler;
        $this->eavConfig = $eavConfig;
    }

    /**
     * @param MagentoProduct $product
     * @param string $size
     * @param string $attributeName
     */
    public function addProductImageAttribute($product, $size = 'product_list_thumbnail', $attributeName = 'thumbnail_resized_url')
    {
        $productExtension = $this->getProductExtensionAttributes($product);
        $productExtension->setData($attributeName, $this->imageHelper->getMainProductImageUrl($product, $size));
        $product->setExtensionAttributes($productExtension);
    }

    /**
     * @param MagentoProduct $product
     */
    public function addMediaGallerySizes($product)
    {
        $this->galleryReadHandler->execute($product);

        $sizes = [];
        $mediaGalleryEntries = $product->getMediaGalleryEntries();
        if(!$mediaGalleryEntries) {
            return;
        }

        $extAttrs = $this->getProductExtensionAttributes($product);

        foreach ($mediaGalleryEntries as $mediaGalleryEntry) {
            if($mediaGalleryEntry->getMediaType() !== 'image' || $mediaGalleryEntry->isDisabled()) {
                continue;
            }

            /** @var GalleryMediaEntrySizeInterface $sizesEntry */
            $sizesEntry = $this->objectManager->create('Hatimeria\Reagento\Api\Data\GalleryMediaEntrySizeInterface');

            $file = $mediaGalleryEntry->getFile();
            $sizesEntry->setFull($this->imageHelper->getProductImageUrl($product, $file, 'product_media_gallery_item'));
            $sizesEntry->setThumbnail($this->imageHelper->getProductImageUrl($product, $file, 'product_media_gallery_item_thumbnail'));
            $sizes[] = $sizesEntry;
        }

        $extAttrs->setMediaGallerySizes($sizes);
        $product->setExtensionAttributes($extAttrs);
    }

    /**
     * Changing "priceCalculation" policy to return a base price for configurable product
     * @param MagentoProduct $product
     */
    public function ensurePriceForConfigurableProduct($product)
    {
        if($product->getTypeId() === 'configurable') {
            $product->setPriceCalculation(false);
        }
    }

    /**
     * @param MagentoProduct $product
     */
    public function ensureOptionsForConfigurableProduct($product)
    {
        /** @var \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry */
        $stockRegistry = $this->objectManager->get('\Magento\CatalogInventory\Api\StockRegistryInterface');

        if($product->getTypeId() === 'configurable') {
            /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable $productInstance */
            $productInstance = $product->getTypeInstance();

            $productExtension = $this->getProductExtensionAttributes($product);
            $stockInfo = [];
            $configurableProductOptions = [];

            /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute[] $attributes */
            $attributes = $productInstance->getConfigurableAttributes($product);

            /** @var array $configurableOptions */
            $configurableOptions = $productInstance->getConfigurableOptions($product);

            foreach ($productInstance->getUsedProducts($product) as $usedProduct) {
                /** @var \Magento\Catalog\Model\Product $usedProduct */
                $stockInfo[$usedProduct->getSku()] = $stockRegistry->getProductStockStatus($usedProduct->getId());
            }

            foreach ($attributes as $attributeItem) {
                $attributeConfigurableOptions = $configurableOptions[$attributeItem->getAttributeId()];
                $attributeOptionValues = [];

                // Getting sort-order data for attribute options
                $attributeOptionsOrder = $productInstance->getAttributeById($attributeItem->getAttributeId(), $product)->getSource()->getAllOptions();
                $optionsOrder = [];

                foreach ($attributeOptionsOrder as $item) {
                    $optionsOrder[] = $item['value'];
                }

                $configurableProductOptions[$attributeItem->getAttributeId()] = [
                    'id' => $attributeItem->getId(),
                    'attribute_id' => $attributeItem->getAttributeId(),
                    'label' => $attributeItem->getLabel(),
                    'position' => $attributeItem->getPosition(),
                    'product_id' => $product->getId(),
                    'values' => [],
                ];

                foreach ($attributeItem->getOptions() as $attributeOption) {
                    $stockProducts = [];
                    foreach ($attributeConfigurableOptions as $attributeConfigurableOption) {
                        if($attributeConfigurableOption['value_index'] === $attributeOption['value_index']) {
                            if(isset($stockInfo[$attributeConfigurableOption['sku']]) && $stockInfo[$attributeConfigurableOption['sku']] > 0) {
                                $stockProducts[] = $attributeConfigurableOption['sku'];
                            }
                        }
                    }

                    /** @var \Magento\ConfigurableProduct\Api\Data\OptionValueInterface $attributeOption */
                    $attributeOptionValues[ array_search($attributeOption['value_index'], $optionsOrder) ] = [
                        'value_index' => $attributeOption['value_index'],
                        'label' => $attributeOption['label'],
                        'in_stock' => $stockProducts,
                    ];
                }
                ksort($attributeOptionValues);
                $configurableProductOptions[$attributeItem->getAttributeId()]['values'] = array_values($attributeOptionValues);
            }

            $productExtension->setConfigurableProductOptions($configurableProductOptions);
            $product->setExtensionAttributes($productExtension);
        }
    }

    /**
     * @param MagentoProduct $product
     * @return ProductExtension|\Magento\Catalog\Api\Data\ProductExtensionInterface
     */
    protected function getProductExtensionAttributes($product)
    {
        $productExtension = $product->getExtensionAttributes();
        if ($productExtension === null) {
            $productExtension = $this->productExtensionFactory->create();
        }

        return $productExtension;
    }
}
