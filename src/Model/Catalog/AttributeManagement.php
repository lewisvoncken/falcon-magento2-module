<?php
namespace Hatimeria\Reagento\Model\Catalog;

use Hatimeria\Reagento\Api\Catalog\AttributeManagementInterface;
use Hatimeria\Reagento\Api\Data\FilterInterface;
use Hatimeria\Reagento\Api\Data\FilterInterfaceFactory;
use Magento\Catalog\Model\Product;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class AttributeManagement implements AttributeManagementInterface
{
    /** @var AttributeRepositoryInterface */
    private $attributeRepository;

    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /** @var FilterInterfaceFactory */
    private $filterFactory;

    /**
     * AttributeManagement constructor.
     * @param AttributeRepositoryInterface $attributeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterInterfaceFactory $filterFactory
     */
    public function __construct(
        AttributeRepositoryInterface $attributeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterInterfaceFactory $filterFactory
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterFactory = $filterFactory;
    }

    public function getCategoryFilters()
    {
        $search = $this->searchCriteriaBuilder->addFilter('is_filterable_in_grid', 1);
        $attributes = $this->attributeRepository->getList(Product::ENTITY, $search->create());

        $result = [];
        foreach($attributes->getItems() as $item) { /** @var AttributeInterface $item */
            $filter = $this->filterFactory->create();
            $filter->setLabel($item->getDefaultFrontendLabel())
                ->setType($item->getBackendType())
                ->setAttributeId($item->getAttributeId())
                ->setCode($item->getAttributeCode())
                ->setOptions($item->getOptions())
            ;
        }

        return $result;
    }
}