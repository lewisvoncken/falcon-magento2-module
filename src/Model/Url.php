<?php

namespace Hatimeria\Reagento\Model;

use Hatimeria\Reagento\Api\UrlInterface;
use Hatimeria\Reagento\Api\Data\UrlDataInterface;
use Hatimeria\Reagento\Helper\Data as ReagentoHelper;
use Hatimeria\Reagento\Model\UrlDataFactory;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\NoSuchEntityException;
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

    public function __construct(
        DataObjectHelper $dataFactory,
        UrlFinderInterface $urlFinder,
        PageRepositoryInterface $pageRepository,
        ProductRepositoryInterface $productRepository,
        CategoryRepositoryInterface $categoryRepository,
        UrlDataFactory $urlDataFactory,
        ReagentoHelper $reagentoHelper
    ) {
        $this->dataFactory = $dataFactory;
        $this->pageRepository = $pageRepository;
        $this->urlFinder = $urlFinder;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->urlDataFactory = $urlDataFactory;
        $this->reagentoHelper = $reagentoHelper;
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
                    $entity = $this->productRepository->getById($urlModel->getEntityId());
                    $this->reagentoHelper->addResponseTagsByObject($entity);
                    $urlData->setProduct($entity);
                    break;

                case 'category':
                    $entity = $this->categoryRepository->get($urlModel->getEntityId());
                    $this->reagentoHelper->addResponseTagsByObject($entity);
                    $urlData->setCategory($entity);
                    break;

                case 'cms-page':
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

            return $urlData;
        }

        throw new NoSuchEntityException(__('Requested entity doesn\'t exist'));
    }
}
