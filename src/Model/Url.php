<?php

namespace Hatimeria\Reagento\Model;

use Hatimeria\Reagento\Api\UrlInterface;
use Hatimeria\Reagento\Api\Data\UrlDataInterface;
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

    public function __construct(
        DataObjectHelper $dataFactory,
        UrlFinderInterface $urlFinder,
        PageRepositoryInterface $pageRepository,
        ProductRepositoryInterface $productRepository,
        CategoryRepositoryInterface $categoryRepository,
        UrlDataFactory $urlDataFactory
    ) {
        $this->dataFactory = $dataFactory;
        $this->pageRepository = $pageRepository;
        $this->urlFinder = $urlFinder;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->urlDataFactory = $urlDataFactory;
    }

    /**
     * @inheritdoc
     */
    public function getUrl($requestPath)
    {
        $urlModel = $this->urlFinder->findOneByData(array('request_path' => $requestPath));

        if ($urlModel) {
            $urlData = $this->urlDataFactory->create();
            $urlData->setEntityType($urlModel->getEntityType());

            switch ($urlModel->getEntityType()) {
                case 'product':
                    $urlData->setProduct($this->productRepository->getById($urlModel->getEntityId()));
                    break;

                case 'category':
                    $urlData->setCategory($this->categoryRepository->get($urlModel->getEntityId()));
                    break;

                case 'cms-page':
                    $urlData->setCmsPage($this->pageRepository->getById($urlModel->getEntityId()));
                    break;
            }

            return $urlData;
        }

        throw new NoSuchEntityException(__('Requested page doesn\'t exist'));
    }
}
