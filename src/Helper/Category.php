<?php

namespace Deity\MagentoApi\Helper;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category as MagentoCategory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context as AppContext;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\View\ConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

class Category extends AbstractHelper
{
    const SHOW_CATEGORY_FILTER_PATH = 'deity/catalog/show_category_filter';

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

    /**
     * Category constructor.
     * @param AppContext $context
     * @param AdapterFactory $imageFactory
     * @param Filesystem $filesystem
     * @param StoreManagerInterface $storeManager
     * @param ConfigInterface $viewConfig
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        AppContext $context,
        AdapterFactory $imageFactory,
        Filesystem $filesystem,
        StoreManagerInterface $storeManager,
        ConfigInterface $viewConfig,
        CategoryRepositoryInterface $categoryRepository
    ) {
        parent::__construct($context);
        $this->viewConfig = $viewConfig;
        $this->storeManager = $storeManager;
        $this->filesystem = $filesystem;
        $this->imageFactory = $imageFactory;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param MagentoCategory $category
     * @param string $size
     * @throws \Exception
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
     * @throws \Exception
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
}
