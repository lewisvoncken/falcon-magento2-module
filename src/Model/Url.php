<?php

namespace Deity\MagentoApi\Model;

use Deity\MagentoApi\Api\UrlInterface;
use Deity\MagentoApi\Helper\Data as DeityHelper;
use Deity\MagentoApi\Helper\Product as DeityProductHelper;
use Deity\MagentoApi\Model\UrlDataFactory;
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
 * @package Deity\MagentoApi\Model
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

    /** @var \Deity\MagentoApi\Model\UrlDataFactory */
    protected $urlDataFactory;

    /** @var DeityHelper */
    protected $deityHelper;

    /** @var DeityProductHelper */
    protected $deityProductHelper;

    /** @var StoreManagerInterface */
    protected $storeManager;

    /**
     * Url constructor.
     * @param DataObjectHelper $dataFactory
     * @param UrlFinderInterface $urlFinder
     * @param PageRepositoryInterface $pageRepository
     * @param ProductRepositoryInterface $productRepository
     * @param CategoryRepositoryInterface $categoryRepository
     * @param \Deity\MagentoApi\Model\UrlDataFactory $urlDataFactory
     * @param DeityHelper $deityHelper
     * @param DeityProductHelper $deityProductHelper
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
        DeityProductHelper $deityProductHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->dataFactory = $dataFactory;
        $this->pageRepository = $pageRepository;
        $this->urlFinder = $urlFinder;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->urlDataFactory = $urlDataFactory;
        $this->deityHelper = $deityHelper;
        $this->deityProductHelper = $deityProductHelper;
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
