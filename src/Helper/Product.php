<?php

namespace Deity\MagentoApi\Helper;

use Deity\MagentoApi\Api\Data\GalleryMediaEntrySizeInterface;
use Deity\MagentoApi\Helper\Media as MediaHelper;
use Deity\MagentoApi\Model\Config\Source\BreadcrumbsAttribute;
use Magento\Catalog\Api\Data\ProductExtension;
use Magento\Catalog\Api\Data\ProductExtensionFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product as MagentoProduct;
use Magento\Catalog\Model\Product\Gallery\ReadHandler as GalleryReadHandler;
use Magento\Eav\Model\Config;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context as AppContext;
use Magento\Framework\ObjectManagerInterface;


/**
 * @package Deity\MagentoApi\Helper
 */
class Product extends AbstractHelper
{
    /** @var MediaHelper */
    protected $mediaHelper;

    /** @var ProductExtensionFactory */
    protected $productExtensionFactory;

    /** @var ObjectManagerInterface */
    protected $objectManager;

    /** @var GalleryReadHandler */
    protected $galleryReadHandler;

    /** @var Price */
    protected $priceHelper;

    /** @var \Magento\Eav\Model\Config */
    protected $eavConfig;

    /**
     * @param AppContext $context
     * @param ProductExtensionFactory $productExtensionFactory
     * @param MediaHelper $mediaHelper
     * @param GalleryReadHandler $galleryReadHandler
     * @param ObjectManagerInterface $objectManager
     * @param Price $priceHelper
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        AppContext $context,
        ProductExtensionFactory $productExtensionFactory,
        MediaHelper $mediaHelper,
        GalleryReadHandler $galleryReadHandler,
        ObjectManagerInterface $objectManager,
        Price $priceHelper,
        Config $eavConfig
    ) {
        parent::__construct($context);
        $this->productExtensionFactory = $productExtensionFactory;
        $this->objectManager = $objectManager;
        $this->mediaHelper = $mediaHelper;
        $this->galleryReadHandler = $galleryReadHandler;
        $this->priceHelper = $priceHelper;
        $this->eavConfig = $eavConfig;
    }

    /**
     * @param MagentoProduct $product
     * @param string $size
     * @param string $attributeName
     */
    public function addProductImageAttribute($product, $size = 'product_list_thumbnail', $attributeName = 'thumbnail_resized_url', $imageName = 'image')
    {
        $productExtension = $this->getProductExtensionAttributes($product);
        $imageUrl = $this->mediaHelper->getProductImageUrl($product, $product->getData($imageName), $size);
        $productExtension->setData($attributeName, $imageUrl ?: '');
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
            if (!$this->isValidMediaGalleryEntry($mediaGalleryEntry)) {
                continue;
            }

            /** @var GalleryMediaEntrySizeInterface $sizesEntry */
            $sizesEntry = $this->objectManager->create('Deity\MagentoApi\Api\Data\GalleryMediaEntrySizeInterface');

            $file = $mediaGalleryEntry->getFile();
            $sizesEntry->setThumbnail($this->mediaHelper->getProductImageUrl($product, $file, 'product_media_gallery_item_thumbnail'));
            $sizesEntry->setFull($this->mediaHelper->getProductImageUrl($product, $file, 'product_media_gallery_item'));
            if ($mediaGalleryEntry->getMediaType() === 'external-video') {
                $sizesEntry->setEmbedUrl($this->mediaHelper->getProductVideoUrl($product, $mediaGalleryEntry->getId()));
            }
            $sizesEntry->setType($mediaGalleryEntry->getMediaType());
            $sizes[] = $sizesEntry;

        }

        $extAttrs->setMediaGallerySizes($sizes);
        $product->setExtensionAttributes($extAttrs);
    }

    /**
     * Validate if media entry can be included in gallery
     *
     * @return bool
     */
    public function isValidMediaGalleryEntry($entity)
    {
        if ($entity->isDisabled()) {
            return false;
        }

        return true;
    }

    /**
     * Changing "priceCalculation" policy to return a base price for configurable product
     * @param MagentoProduct $product
     */
    public function ensurePriceForConfigurableProduct($product)
    {
        if($product->getTypeId() === 'configurable') {
            $product->setPriceCalculation(false);
            $product->setPrice($product->getFinalPrice());
        }
    }

    /**
     * @param MagentoProduct $product
     * @throws \Magento\Framework\Exception\LocalizedException
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
            $disabledProducts = [];
            $configurableProductOptions = [];

            /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute[] $attributes */
            $attributes = $productInstance->getConfigurableAttributes($product);

            /** @var array $configurableOptions */
            $configurableOptions = $productInstance->getConfigurableOptions($product);

            foreach ($productInstance->getUsedProducts($product) as $usedProduct) {
                /** @var \Magento\Catalog\Model\Product $usedProduct */
                $stockInfo[$usedProduct->getSku()] = $stockRegistry->getProductStockStatus($usedProduct->getId());
                if($usedProduct->getStatus() != 1) {
                    $disabledProducts[] = $usedProduct->getSku();
                }
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
                    $optionEnabled = true;
                    $stockProducts = [];
                    foreach ($attributeConfigurableOptions as $attributeConfigurableOption) {
                        if($attributeConfigurableOption['value_index'] === $attributeOption['value_index']) {
                            if(in_array($attributeConfigurableOption['sku'], $disabledProducts)) {
                                $optionEnabled = false;
                                break;
                            }

                            if(isset($stockInfo[$attributeConfigurableOption['sku']]) && $stockInfo[$attributeConfigurableOption['sku']] > 0) {
                                $stockProducts[] = $attributeConfigurableOption['sku'];
                            }
                        }
                    }

                    if(!$optionEnabled) {
                        continue;
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
     * @param MagentoProduct|ProductInterface $product
     * @return ProductExtension|\Magento\Catalog\Api\Data\ProductExtensionInterface
     */
    protected function getProductExtensionAttributes(ProductInterface $product)
    {
        $productExtension = $product->getExtensionAttributes();
        if ($productExtension === null) {
            $productExtension = $this->productExtensionFactory->create();
        }

        return $productExtension;
    }

    /**
     * @param MagentoProduct $product
     */
    public function calculateCatalogDisplayPrice($product)
    {
        $displayPrice = $this->priceHelper->calculateCatalogDisplayPrice($product);
        $productExtension = $this->getProductExtensionAttributes($product);
        $productExtension->setCatalogDisplayPrice($displayPrice['calculated_price']);
        $productExtension->setMinPrice($displayPrice['min_price']);
        $productExtension->setMaxPrice($displayPrice['max_price']);
        $product->setExtensionAttributes($productExtension);
    }

    /**
     * Get list of attributes used in filters from config
     *
     * @return array
     */
    public function getFilterableAttributes()
    {
        $attributes = [];
        if ($config = $this->scopeConfig->getValue(BreadcrumbsAttribute::BREADCRUMBS_ATTRIBUTES_CONFIG_PATH)) {
            $attributes = explode(',', $config);
        }

        return $attributes;
    }
}
