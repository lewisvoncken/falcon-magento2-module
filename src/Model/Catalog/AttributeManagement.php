<?php
namespace Deity\MagentoApi\Model\Catalog;

use Deity\MagentoApi\Api\Catalog\AttributeManagementInterface;
use Deity\MagentoApi\Api\Data\FilterInterface;
use Deity\MagentoApi\Api\Data\FilterInterfaceFactory;
use Deity\MagentoApi\Api\Data\FilterOptionInterface;
use Deity\MagentoApi\Api\Data\FilterOptionInterfaceFactory;
use Deity\MagentoApi\Helper\Category;
use Magento\Catalog\Model\Product;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Eav\Api\Data\AttributeOptionInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class AttributeManagement implements AttributeManagementInterface
{
    /** @var AttributeRepositoryInterface */
    private $attributeRepository;

    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /** @var FilterInterfaceFactory */
    private $filterFactory;

    /** @var FilterOptionInterfaceFactory */
    private $filterOptionFactory;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /**
     * AttributeManagement constructor.
     * @param AttributeRepositoryInterface $attributeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterInterfaceFactory $filterFactory
     * @param FilterOptionInterfaceFactory $filterOptionFactory
     */
    public function __construct(
        AttributeRepositoryInterface $attributeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterInterfaceFactory $filterFactory,
        FilterOptionInterfaceFactory $filterOptionFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterFactory = $filterFactory;
        $this->filterOptionFactory = $filterOptionFactory;
        $this->scopeConfig = $scopeConfig;
    }

    public function getCategoryFilters()
    {
        $search = $this->searchCriteriaBuilder->addFilter('is_filterable', 0, 'neq');
        $attributes = $this->attributeRepository->getList(Product::ENTITY, $search->create());

        $result = [];
        if ($this->scopeConfig->getValue(Category::SHOW_CATEGORY_FILTER_PATH, ScopeInterface::SCOPE_STORES)) {
            $result[] = $this->getCategoryFilter();
        }
        foreach($attributes->getItems() as $item) { /** @var AttributeInterface $item */
            $filter = $this->filterFactory->create();
            $options = $item->usesSource() ? $item->getSource()->getAllOptions(false) : null;
            if (empty($options)) {
                continue;
            }
            $filter->setLabel($item->getDefaultFrontendLabel())
                ->setType($item->getBackendType())
                ->setAttributeId($item->getAttributeId())
                ->setCode($item->getAttributeCode())
                ->setOptions($this->prepareFilterOptions($options))
            ;
            $result[] = $filter;
        }

        return $result;
    }

    /**
     * @param array $options
     * @return FilterOptionInterface[]
     */
    protected function prepareFilterOptions($options)
    {
        $filterOptions = [];
        foreach($options as $option) { /** @var AttributeOptionInterface $option */
            /** @var FilterInterface $filterOption */
            $filterOption = $this->filterOptionFactory->create();
            $filterOption->setLabel($option['label'])
                ->setValue($option['value']);
            $filterOptions[] = $filterOption;
        }

        return $filterOptions;
    }

    /**
     * Prepare category filter
     *
     * @return FilterInterface
     */
    protected function getCategoryFilter()
    {
        $filter = $this->filterFactory->create();
        $filter->setLabel(__('Category'))
            ->setCode('category_id');

        return $filter;
    }
}