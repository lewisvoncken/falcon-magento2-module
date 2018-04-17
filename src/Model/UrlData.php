<?php

namespace Deity\MagentoApi\Model;

use Deity\MagentoApi\Api\Data\UrlDataInterface;
use Magento\Framework\DataObject;

/**
 * @package Deity\MagentoApi\Model
 */
class UrlData extends DataObject implements UrlDataInterface
{
    /**
     * @inheritdoc
     */
    public function getEntityType()
    {
        return $this->getData(self::ENTITY_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setEntityType($entityType)
    {
        return $this->setData(self::ENTITY_TYPE, $entityType);
    }

    /**
     * @inheritdoc
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getCmsPage()
    {
        return $this->getData(self::CMS_PAGE);
    }

    /**
     * @inheritdoc
     */
    public function setCmsPage(\Magento\Cms\Api\Data\PageInterface $page)
    {
        return $this->setData(self::CMS_PAGE, $page);
    }

    /**
     * @inheritdoc
     */
    public function getProduct()
    {
        return $this->getData(self::PRODUCT);
    }

    /**
     * @inheritdoc
     */
    public function setProduct(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        return $this->setData(self::PRODUCT, $product);
    }

    /**
     * @inheritdoc
     */
    public function getCategory()
    {
        return $this->getData(self::CATEGORY);
    }

    /**
     * @inheritdoc
     */
    public function setCategory(\Magento\Catalog\Api\Data\CategoryInterface $category)
    {
        return $this->setData(self::CATEGORY, $category);
    }
}
