<?php

namespace Hatimeria\Reagento\Helper;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category as MagentoCategory;
use Magento\Catalog\Model\Category\Collection as MagentoCategoryCollection;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context as AppContext;

class Category extends AbstractHelper
{
    const SHOW_CATEGORY_FILTER_PATH = 'reagento/catalog/show_category_filter';

    /** @var \Magento\Framework\View\ConfigInterface */
    private $viewConfig;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $storeManager;

    /** @var \Magento\Framework\Filesystem */
    private $filesystem;

    /** @var \Magento\Framework\Image\AdapterFactory */
    private $imageFactory;

    /** @var CategoryRepositoryInterface */
    protected $categoryRepository;

    /** @var \Magento\Catalog\Api\Data\CategoryExtensionFactory */
    protected $extensionFactory;

    protected $loadedCategories = [];

    /**
     * @param AppContext $context
     * @param \Magento\Framework\Image\AdapterFactory $imageFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\ConfigInterface $viewConfig
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(AppContext $context,
                                \Magento\Framework\Image\AdapterFactory $imageFactory,
                                \Magento\Catalog\Api\Data\CategoryExtensionFactory $extensionFactory,
                                \Magento\Framework\Filesystem $filesystem,
                                \Magento\Store\Model\StoreManagerInterface $storeManager,
                                \Magento\Framework\View\ConfigInterface $viewConfig,
                                CategoryRepositoryInterface $categoryRepository
    ) {
        parent::__construct($context);
        $this->viewConfig = $viewConfig;
        $this->storeManager = $storeManager;
        $this->filesystem = $filesystem;
        $this->imageFactory = $imageFactory;
        $this->categoryRepository = $categoryRepository;
        $this->extensionFactory = $extensionFactory;
    }

    /**
     * @param MagentoCategory $category
     * @param string $size
     */
    public function addImageAttribute($category, $size = 'category_page_grid')
    {
        $this->convertImageAttributeToUrl($category, 'image', $size);
    }

    /**
     * Convert category object image attribute to resized full url value
     *
     * @param MagentoCategory $category
     * @param string $attribute Attribute name for convertion
     * @param string $size
     * @param string $imagePath
     */
    public function convertImageAttributeToUrl($category, $attribute = 'image',  $size = 'category_page_grid', $imagePath = 'catalog/category/')
    {
        $sizeValues = $this->viewConfig->getViewConfig()->getMediaAttributes('Magento_Catalog', 'images', $size);
        $imageName = $category->getData($attribute);
        if(!$imageName || !$sizeValues) {
            return;
        }

        $height = $sizeValues['height'];
        $width = $sizeValues['width'];
        // TODO try do not use hardcoded paths
        $categorySubPath = $imagePath;

        $absolutePath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($categorySubPath);
        $resizedImagePath = "cache/{$width}x{$height}/{$imageName}";
        $resizedImage = $absolutePath . $resizedImagePath;
        $originalImagePath = $absolutePath . $imageName;

        if(!file_exists($originalImagePath)) {
            return;
        }

        if(!file_exists($resizedImage)) {
            $imageResize = $this->imageFactory->create();
            $imageResize->open($originalImagePath);
            $imageResize->constrainOnly(TRUE);
            $imageResize->keepTransparency(TRUE);
            $imageResize->keepFrame(FALSE);
            $imageResize->keepAspectRatio(TRUE);
            $imageResize->resize($width, $height);
            $imageResize->save($resizedImage);
        }

        $url = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
            . $categorySubPath . $resizedImagePath;

        $category->setData($attribute, $url);
    }

    /**
     * @param MagentoCategory $category
     * @param MagentoCategoryCollection $collection
     */
    public function addBreadcrumbsData($category, $collection = null)
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

            $result[] = [
                'id' => $parentCategory->getId(),
                'name' => $parentCategory->getName(),
                'url_path' => $parentCategory->getUrlPath(),
                'url_key' => $parentCategory->getUrlKey()
            ];
        }

        $result[] = [
            'id' => $category->getId(),
            'name' => $category->getName(),
            'url_path' => $category->getUrlPath(),
            'url_key' => $category->getUrlKey()
        ];

        $extensionAttributes = $category->getExtensionAttributes();
        if($extensionAttributes === null) {
            $extensionAttributes = $this->extensionFactory->create();
        }

        $extensionAttributes->setData('breadcrumbs', $result);
        $category->setExtensionAttributes($extensionAttributes);
    }
}
