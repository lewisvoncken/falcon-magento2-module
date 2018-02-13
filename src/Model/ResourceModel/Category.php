<?php
namespace Deity\MagentoApi\Model\ResourceModel;

use Magento\Framework\EntityManager\EntityManager;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\ResourceModel\Category as MagentoCategory;

class Category extends MagentoCategory
{
    /**
     * Return parent categories of category
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return \Magento\Framework\DataObject[]
     */
    public function getParentCategories($category)
    {
        $pathIds = array_reverse(explode(',', $category->getPathInStore()));
        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $categories */
        $categories = $this->_categoryCollectionFactory->create();
        return $categories->setStore(
            $this->_storeManager->getStore()
        )->addAttributeToSelect(
            'name'
        )->addAttributeToSelect(
            'url_key'
        )->addAttributeToSelect(
            'url_path'
        )->addFieldToFilter(
            'entity_id',
            ['in' => $pathIds]
        )->addFieldToFilter(
            'is_active',
            1
        )->load()->getItems();
    }
}
