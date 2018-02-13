<?php

namespace Deity\MagentoApi\Plugin\CatalogUrlRewrite\Observer;

use Deity\MagentoApi\Helper\Data;
use Magento\Catalog\Model\Category;
use Magento\CatalogUrlRewrite\Observer\UrlRewriteHandler as MagentoUrlRewriteHandler;


class UrlRewriteHandler
{
    /** @var Data */
    protected $helper;

    /**
     * UrlRewriteHandler constructor.
     *
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Prevent generating category product url if setting is enabled
     *
     * @param MagentoUrlRewriteHandler $subject
     * @param callable $proceed
     * @param Category $category
     * @return array
     */
    public function aroundGenerateProductUrlRewrites(
        MagentoUrlRewriteHandler $subject,
        callable $proceed,
        Category $category
    ) {
        $storeId = $category->getStoreId();

        if ($this->helper->shouldGenerateProductUrls($storeId)) {
            return $proceed($category);
        }

        return [];
    }
}