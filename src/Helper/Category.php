<?php

namespace Hatimeria\Reagento\Helper;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category as MagentoCategory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context as AppContext;

class Category extends AbstractHelper
{
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
        $sizeValues = $this->viewConfig->getViewConfig()->getMediaAttributes('Magento_Catalog', 'images', $size);
        $imageName = $category->getData('image');
        if(!$imageName || !$sizeValues) {
            return;
        }

        $height = $sizeValues['height'];
        $width = $sizeValues['width'];
        // TODO try do not use hardcoded paths
        $categorySubPath = 'catalog/category/';

        $absolutePath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($categorySubPath);
        $resizedImagePath = "cache/{$width}x{$height}/{$imageName}";
        $resizedImage = $absolutePath . $resizedImagePath;

        if(!file_exists($resizedImage)) {
            $imageResize = $this->imageFactory->create();
            $imageResize->open($absolutePath . $imageName);
            $imageResize->constrainOnly(TRUE);
            $imageResize->keepTransparency(TRUE);
            $imageResize->keepFrame(FALSE);
            $imageResize->keepAspectRatio(TRUE);
            $imageResize->resize($width, $height);
            $imageResize->save($resizedImage);
        }

        $url = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
            . $categorySubPath . $resizedImagePath;

        $category->setData('image', $url);
    }

    /**
     * @param MagentoCategory $category
     */
    public function ensureUrlPath($category)
    {
        /** @var MagentoCategory $fullEntity */
        $fullEntity = $this->categoryRepository->get($category->getId());
        $category->setData('url_path', $fullEntity->getData('url_path'));
    }

    /**
     * @param MagentoCategory $category
     */
    public function addBreadcrumbsData($category)
    {
        $pathInStore = $category->getPathInStore();
        if(empty($pathInStore)) {
            return;
        }

        $pathIds = array_reverse(explode(',', $pathInStore));

        $result = [];

        // If there's only 1 category in the path and it equals to the current one - do nothing.
        if(count($pathIds) == 1 && $pathIds[0] == $category->getId()) {
            return;
        }

        foreach ($pathIds as $categoryId) {
            // Skip category information about current category
            if($categoryId === $category->getId()) {
                continue;
            }

            /** @var MagentoCategory $parentCategory */
            $parentCategory = $this->categoryRepository->get($categoryId);

            $result[] = [
                'id' => $parentCategory->getId(),
                'name' => $parentCategory->getName(),
                'url_path' => $parentCategory->getUrlPath(),
                'url_key' => $parentCategory->getUrlKey()
            ];
        }

        $extensionAttributes = $category->getExtensionAttributes();
        if($extensionAttributes === null) {
            $extensionAttributes = $this->extensionFactory->create();
        }

        $extensionAttributes->setData('breadcrumbs', $result);
        $category->setExtensionAttributes($extensionAttributes);
    }
}