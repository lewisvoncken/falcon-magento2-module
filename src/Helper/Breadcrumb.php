<?php
namespace Deity\MagentoApi\Helper;

use Deity\MagentoApi\Api\Data\BreadcrumbInterface;
use Deity\MagentoApi\Api\Data\BreadcrumbInterfaceFactory;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Breadcrumb extends AbstractHelper
{
    /** @var StoreManagerInterface */
    protected $storeManager;

    /** @var CategoryRepositoryInterface */
    protected $categoryRepository;

    /** @var BreadcrumbInterfaceFactory */
    protected $breadcrumbFactory;

    /** @var ExtensionAttributesFactory */
    protected $extensionAttributesFactory;

    /** @var CategoryModel[] */
    protected $loadedCategories = [];

    /**
     * Breadcrumb constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param CategoryRepositoryInterface $categoryRepository
     * @param BreadcrumbInterfaceFactory $breadcrumbFactory
     * @param ExtensionAttributesFactory $extensionAttributesFactory
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        CategoryRepositoryInterface $categoryRepository,
        BreadcrumbInterfaceFactory $breadcrumbFactory,
        ExtensionAttributesFactory $extensionAttributesFactory
    ) {
        parent::__construct($context);
        $this->categoryRepository = $categoryRepository;
        $this->breadcrumbFactory = $breadcrumbFactory;
        $this->extensionAttributesFactory = $extensionAttributesFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @param mixed $data
     * @return BreadcrumbInterface
     */
    public function createBreadcrumb($data)
    {
        /** @var BreadcrumbInterface $breadcrumb */
        $breadcrumb = $this->breadcrumbFactory->create();
        $breadcrumb->loadFromData($data);

        return $breadcrumb;
    }

    /**
     * Add breadcrumb data to category
     *
     * @param CategoryModel $category
     * @param CategoryCollection|null $collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function addCategoryBreadcrumbs(CategoryModel $category, CategoryCollection $collection = null)
    {
        $pathInStore = $category->getPathInStore();
        if(empty($pathInStore)) {
            return;
        }

        $result = [];

        $pathIds = array_reverse(explode(',', $pathInStore));
        array_pop($pathIds); // remove the current category from parent path ids

        foreach ($pathIds as $id) {
            if($collection) {
                $parentCategory = $collection->getItemById($id);
            } else if(array_key_exists($id, $this->loadedCategories)) {
                $parentCategory = $this->loadedCategories[$id];
            } else {
                $parentCategory = $this->categoryRepository->get($id);
                $this->loadedCategories[$id] = $parentCategory;
            }

            // todo: category may not be found - investigate why!
            if(!$parentCategory) {
                continue;
            }

            $result[] = $this->createBreadcrumb($this->prepareCategoryBreadcrumbData($parentCategory));
        }

        $result[] = $this->createBreadcrumb($this->prepareCategoryBreadcrumbData($category));

        $extensionAttributes = $category->getExtensionAttributes();
        if($extensionAttributes === null) {
            $extensionAttributes = $this->extensionAttributesFactory->create(CategoryInterface::class);
        }

        $extensionAttributes->setData('breadcrumbs', $result);
        $category->setExtensionAttributes($extensionAttributes);
    }


    /**
     * Add breadcrumb data to product
     *
     * @param ProductModel|ProductInterface $product
     * @param string[] $filters
     */
    public function addProductBreadcrumbsData(ProductInterface $product, $filters = [])
    {
        $categories = $product->getCategoryIds();
        $categoryId = array_shift($categories);

        $breadcrumbs = $categoryId ? $this->processCategoryBreadcrumbs($product, $categoryId, $filters) : [];
        $breadcrumbs[] = $this->createBreadcrumb([
            'name' => $product->getName()
        ]);

        $productExtension = $product->getExtensionAttributes();
        if (!$productExtension) {
            $productExtension = $this->extensionAttributesFactory->create(ProductInterface::class);
        }
        $productExtension->setBreadcrumbs($breadcrumbs);
        $product->setExtensionAttributes($productExtension);
    }

    /**
     * @param ProductInterface $product
     * @param $categoryId
     * @param $filters
     * @return array
     */
    protected function processCategoryBreadcrumbs(ProductInterface $product, $categoryId, $filters)
    {
        $useSubcategoryFilter = $this->scopeConfig->getValue(
            \Deity\MagentoApi\Helper\Category::SHOW_CATEGORY_FILTER_PATH,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );
        $breadcrumbs = [];

        /** @var CategoryModel $category */
        try {
            $category = $this->categoryRepository->get($categoryId);
        } catch (\Exception $e) {
            $this->_logger->critical($e);
            return $breadcrumbs;
        }

        $categoryExtensionAttributes = $category->getExtensionAttributes();
        if ($categoryExtensionAttributes) {
            $breadcrumbs = $categoryExtensionAttributes->getBreadcrumbs();
        }

        $categoryCrumb = end($breadcrumbs);
        foreach($breadcrumbs as $id => $crumb) { /** @var BreadcrumbInterface $crumb */
            if ($crumb->getId() === $categoryId) {
                if ($useSubcategoryFilter) {
                    //change subcategory url to use subcategory filter instead of link to subcategory page
                    $prev = ($id > 0 ? $id : 1) - 1;
                    $parentCategory = $breadcrumbs[$prev];
                    $crumb->setUrlPath($parentCategory->getUrlPath());
                    $crumb->setUrlQuery(['filters' => ['in_category' => $categoryId]]);
                }
                $categoryCrumb = $crumb;
                break;
            }
        }

        foreach ($filters as $attribute) {
            if ($product->hasData($attribute)) {
                $attributeValue = $product->getData($attribute);
                $attributeLabel = $product->getAttributeText($attribute);
                if (is_array($attributeLabel)) {
                    $attributeLabel = implode(', ', $attributeLabel);
                }
                $categoryCrumbFilters = $useSubcategoryFilter ? $categoryCrumb->getUrlQuery()['filters'] : [];
                $attributeCrumb['name'] = $attributeLabel;
                $attributeCrumb['url_path'] = $categoryCrumb->getUrlPath();
                $attributeCrumb['url_query']['filters'] = $categoryCrumbFilters + [$attribute => $attributeValue];
                $breadcrumbs[] = $this->createBreadcrumb($attributeCrumb);
            }
        }

        return $breadcrumbs;
    }

    /**
     * Prepare category data for breadcrumbs
     *
     * @param CategoryModel $category
     * @return array
     */
    protected function prepareCategoryBreadcrumbData(CategoryModel $category)
    {
        return [
            'id' => $category->getId(),
            'name' => $category->getName(),
            'url_path' => $category->getUrlPath(),
            'url_key' => $category->getUrlKey()
        ];
    }
}