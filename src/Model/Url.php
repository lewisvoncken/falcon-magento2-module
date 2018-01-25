<?php

namespace Hatimeria\Reagento\Model;

use Hatimeria\Reagento\Api\UrlInterface;
use Hatimeria\Reagento\Api\Data\UrlDataInterface;
use Hatimeria\Reagento\Helper\Data as ReagentoHelper;
use Hatimeria\Reagento\Helper\Product as ReagentoProductHelper;
use Hatimeria\Reagento\Model\UrlDataFactory;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Cms\Model\Page;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;

/**
 * @package Hatimeria\Reagento\Model
 */
class Url implements UrlInterface
{
    protected $pageRepository;
    protected $dataFactory;
    protected $urlFinder;

    /** @var \Magento\Catalog\Api\ProductRepositoryInterface */
    protected $productRepository;

    /** @var \Magento\Catalog\Api\CategoryRepository */
    protected $categoryRepository;

    /** @var \Hatimeria\Reagento\Model\UrlDataFactory */
    protected $urlDataFactory;
    /**
     * @var ReagentoHelper
     */
    protected $reagentoHelper;
    /**
     * @var ReagentoProductHelper
     */
    protected $reagentoProductHelper;

    /** @var StoreManagerInterface */
    protected $storeManager;

    /**
     * Url constructor.
     * @param DataObjectHelper $dataFactory
     * @param UrlFinderInterface $urlFinder
     * @param PageRepositoryInterface $pageRepository
     * @param ProductRepositoryInterface $productRepository
     * @param CategoryRepositoryInterface $categoryRepository
     * @param \Hatimeria\Reagento\Model\UrlDataFactory $urlDataFactory
     * @param ReagentoHelper $reagentoHelper
     * @param ReagentoProductHelper $reagentoProductHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        DataObjectHelper $dataFactory,
        UrlFinderInterface $urlFinder,
        PageRepositoryInterface $pageRepository,
        ProductRepositoryInterface $productRepository,
        CategoryRepositoryInterface $categoryRepository,
        UrlDataFactory $urlDataFactory,
        ReagentoHelper $reagentoHelper,
        ReagentoProductHelper $reagentoProductHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->dataFactory = $dataFactory;
        $this->pageRepository = $pageRepository;
        $this->urlFinder = $urlFinder;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->urlDataFactory = $urlDataFactory;
        $this->reagentoHelper = $reagentoHelper;
        $this->reagentoProductHelper = $reagentoProductHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function getUrl($requestPath, $secondCheck = false)
    {
        $urlModel = $this->urlFinder->findOneByData(array('request_path' => $requestPath));

        if ($urlModel) {
            $urlData = $this->urlDataFactory->create();
            $urlData->setEntityType($urlModel->getEntityType());

            switch ($urlModel->getEntityType()) {
                case 'product':
                    /** @var Product|ProductInterface $entity */
                    $entity = $this->productRepository->getById($urlModel->getEntityId(), false, $this->getCurrentStoreId());
                    $this->reagentoProductHelper->addAdditionalInformation($entity);
                    $this->reagentoHelper->addResponseTagsByObject($entity);
                    $urlData->setProduct($entity);
                    break;

                case 'category':
                    /** @var \Magento\Catalog\Model\Category|CategoryInterface $entity */
                    $entity = $this->categoryRepository->get($urlModel->getEntityId(), $this->getCurrentStoreId());
                    $this->reagentoHelper->addResponseTagsByObject($entity);
                    $urlData->setCategory($entity);
                    break;

                case 'cms-page':
                    /** @var Page|PageInterface $entity */
                    $entity = $this->pageRepository->getById($urlModel->getEntityId());
                    $this->reagentoHelper->addResponseTagsByObject($entity);
                    $urlData->setCmsPage($entity);
                    break;

                case 'custom':
                    // Preventing multiple checks
                    if(!$secondCheck) {
                        return $this->getUrl($urlModel->getTargetPath(), true);
                    }
                    break;
            }

            if (isset($entity)) {
                $this->validateEntityAvailableInStore($entity, $urlData->getEntityType());
            }

            return $urlData;
        }

        throw new NoSuchEntityException(__('Requested entity doesn\'t exist'));
    }

    /**
     * Get current store id
     *
     * @return int
     */
    protected function getCurrentStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * Check if entity is accessible from frontend
     *
     * @param $entity
     * @param string $entityType
     * @throws NoSuchEntityException
     */
    protected function validateEntityAvailableInStore($entity, $entityType)
    {
        switch ($entityType) {
            case 'product':
                /** @var Product $entity */
                if ((int)$entity->getStatus() === Product\Attribute\Source\Status::STATUS_DISABLED) {
                    throw new NoSuchEntityException(__('Requested product doesn\'t exist'));
                }
            case 'product':
            case 'category':
                if (!in_array($this->getCurrentStoreId(), $entity->getStoreIds())) {
                    throw new NoSuchEntityException(__('Requested product doesn\'t exist'));
                }
                break;
            case 'cms-page':
                /** @var Page $entity */
                if (!$entity->isActive() || !in_array($this->getCurrentStoreId(), $entity->getStores())) {
                    throw new NoSuchEntityException(__('Requested product doesn\'t exist'));
                }
                break;
        }
    }
}
