<?php

namespace Deity\MagentoApi\Model;

use Deity\MagentoApi\Api\Data\UrlDataInterface;
use Deity\MagentoApi\Api\UrlInterface;
use Deity\MagentoApi\Helper\Data as DeityHelper;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Cms\Model\Page;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;

/**
 * @package Deity\MagentoApi\Model
 */
class Url implements UrlInterface
{
    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @var DataObjectHelper
     */
    private $dataFactory;

    /**
     * @var UrlFinderInterface
     */
    private $urlFinder;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var UrlDataFactory
     */
    private $urlDataFactory;

    /**
     * @var DeityHelper
     */
    private $deityHelper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Url constructor.
     * @param DataObjectHelper $dataFactory
     * @param UrlFinderInterface $urlFinder
     * @param PageRepositoryInterface $pageRepository
     * @param ProductRepositoryInterface $productRepository
     * @param CategoryRepositoryInterface $categoryRepository
     * @param UrlDataFactory $urlDataFactory
     * @param DeityHelper $deityHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        DataObjectHelper $dataFactory,
        UrlFinderInterface $urlFinder,
        PageRepositoryInterface $pageRepository,
        ProductRepositoryInterface $productRepository,
        CategoryRepositoryInterface $categoryRepository,
        UrlDataFactory $urlDataFactory,
        DeityHelper $deityHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->dataFactory = $dataFactory;
        $this->pageRepository = $pageRepository;
        $this->urlFinder = $urlFinder;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->urlDataFactory = $urlDataFactory;
        $this->deityHelper = $deityHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * @param string $requestPath
     * @param bool $loadEntityData
     * @param bool $secondCheck
     * @return UrlDataInterface|UrlData
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getUrl($requestPath, $loadEntityData = true, $secondCheck = false)
    {
        $urlModel = $this->getUrlModel($requestPath);

        $urlData = $this->urlDataFactory->create();
        $urlData->setEntityType($urlModel->getEntityType());
        $urlData->setEntityId($urlModel->getEntityId());

        if (!$loadEntityData) {
            return $urlData;
        }

        switch ($urlModel->getEntityType()) {
            case 'product':
                /** @var Product|ProductInterface $entity */
                $entity = $this->productRepository->getById($urlModel->getEntityId(), false, $this->getCurrentStoreId());
                $this->deityHelper->addResponseTagsByObject($entity);
                $urlData->setProduct($entity);
                break;

            case 'category':
                /** @var \Magento\Catalog\Model\Category|CategoryInterface $entity */
                $entity = $this->categoryRepository->get($urlModel->getEntityId(), $this->getCurrentStoreId());
                $this->deityHelper->addResponseTagsByObject($entity);
                $urlData->setCategory($entity);
                break;

            case 'cms-page':
                /** @var Page|PageInterface $entity */
                $entity = $this->pageRepository->getById($urlModel->getEntityId());
                $this->deityHelper->addResponseTagsByObject($entity);
                $urlData->setCmsPage($entity);
                break;

            case 'custom':
                // Preventing multiple checks
                if (!$secondCheck) {
                    return $this->getUrl($urlModel->getTargetPath(), true);
                }
                break;
        }

        if (isset($entity)) {
            $this->validateEntityAvailableInStore($entity, $urlData->getEntityType());
        }

        return $urlData;
    }

    /**
     * @param $path
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite|null
     * @throws NoSuchEntityException
     */
    private function getUrlModel($path)
    {
        $urlModel = $this->urlFinder->findOneByData(['request_path' => $path]);

        if (!$urlModel) {
            $urlModel = $this->urlFinder->findOneByData(['target_path' => $path]);
        }

        if (!$urlModel) {
            throw new NoSuchEntityException(__('Requested entity doesn\'t exist'));
        }

        return $urlModel;
    }

    /**
     * Get current store id
     *
     * @return int
     */
    private function getCurrentStoreId()
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
    private function validateEntityAvailableInStore($entity, $entityType)
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
