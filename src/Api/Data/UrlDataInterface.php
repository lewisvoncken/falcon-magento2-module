<?php

namespace Deity\MagentoApi\Api\Data;

interface UrlDataInterface
{
    const ENTITY_TYPE = 'entity_type';
    const ENTITY_ID   = 'entity_id';
    const CMS_PAGE    = 'cms_page';
    const PRODUCT     = 'product';
    const CATEGORY    = 'category';

    /**
     * @return string
     */
    public function getEntityType();

    /**
     * @param string $entityType
     * @return \Deity\MagentoApi\Api\Data\UrlDataInterface
     */
    public function setEntityType($entityType);

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $id
     * @return \Deity\MagentoApi\Api\Data\UrlDataInterface
     */
    public function setEntityId($id);

    /**
     * @return \Magento\Cms\Api\Data\PageInterface
     */
    public function getCmsPage();

    /**
     * @param \Magento\Cms\Api\Data\PageInterface $page
     * @return $this
     */
    public function setCmsPage(\Magento\Cms\Api\Data\PageInterface $page);

    /**
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    public function getProduct();

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return $this
     */
    public function setProduct(\Magento\Catalog\Api\Data\ProductInterface $product);

    /**
     * @return \Magento\Catalog\Api\Data\CategoryInterface
     */
    public function getCategory();

    /**
     * @param \Magento\Catalog\Api\Data\CategoryInterface $category
     * @return $this
     */
    public function setCategory(\Magento\Catalog\Api\Data\CategoryInterface $category);
}
