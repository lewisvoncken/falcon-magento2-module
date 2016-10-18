<?php

namespace Hatimeria\Reagento\Model;

use Hatimeria\Reagento\Api\UrlInterface;
use Magento\Cms\Model\PageRepository;
use Magento\Cms\Model\ResourceModel\Page;
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

    public function __construct(
        \Magento\Framework\Api\DataObjectHelper $dataFactory,
        UrlFinderInterface $urlFinder,
        PageRepository $pageRepository
    ) {
        $this->dataFactory = $dataFactory;
        $this->pageRepository = $pageRepository;
        $this->urlFinder = $urlFinder;
    }

    /**
     * @return Page
     */
    public function getUrl($url)
    {
        $urlModel = $this->urlFinder->findOneByData(array('request_path' => $url));

        if ($urlModel && $urlModel->getEntityType() === 'cms-page') {
            return $this->pageRepository->getById($urlModel->getEntityId());
        }

        throw new NoSuchEntityException(__('Requested page doesn\'t exist'));
    }
}
