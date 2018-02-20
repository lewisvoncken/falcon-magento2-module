<?php

namespace Deity\MagentoApi\Helper;

use Magento\Cms\Model\Block;
use Magento\Cms\Model\Page;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context as AppContext;

class Cms extends AbstractHelper
{
    /** @var FilterProvider */
    private $filterProvider;

    /**
     * Cms constructor.
     * @param AppContext $context
     * @param FilterProvider $filterProvider
     */
    public function __construct(AppContext $context, FilterProvider $filterProvider)
    {
        parent::__construct($context);
        $this->filterProvider = $filterProvider;
    }

    /**
     * @param Page|Block $pageOrBlock
     */
    public function filterEntityContent($pageOrBlock)
    {
        /** @var \Magento\Framework\Filter\Template $filter */
        $filter = $pageOrBlock instanceof Page ? $this->filterProvider->getPageFilter() : $this->filterProvider->getBlockFilter();

        $pageOrBlock->setContent($filter->filter($pageOrBlock->getContent()));
    }
}