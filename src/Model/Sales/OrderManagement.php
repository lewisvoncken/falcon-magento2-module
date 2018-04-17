<?php

namespace Deity\MagentoApi\Model\Sales;

use Deity\MagentoApi\Api\Sales\OrderManagementInterface;
use Deity\MagentoApi\Model\Sales\Order\Extension as OrderExtension;
use Deity\MagentoApi\Model\Sales\Order\Item\Extension as OrderItemExtension;
use Deity\MagentoApi\Model\Sales\Order\Payment\Extension as OrderPaymentExtension;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Manager;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterfaceFactory as SearchResultFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order\Payment\Collection as OrderPaymentCollection;
use Magento\Sales\Model\ResourceModel\Order\Payment\CollectionFactory as OrderPaymentCollectionFactory;

class OrderManagement implements OrderManagementInterface
{

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var UserContextInterface */
    private $userContext;

    /** @var SearchResultFactory */
    private $searchResultFactory;

    /** @var OrderExtension */
    private $orderExtension;

    /** @var OrderItemExtension */
    private $orderItemExtension;

    /** @var OrderPaymentExtension */
    private $orderPaymentExtension;

    /** @var Manager */
    private $eventManager;

    /** @var OrderPaymentCollectionFactory */
    private $orderPaymentCollectionFactory;

    /**
     * OrderManagement constructor.
     * @param OrderRepositoryInterface $orderRepository
     * @param UserContextInterface $userContext
     * @param SearchResultFactory $searchResultFactory
     * @param OrderExtension $orderExtension
     * @param OrderItemExtension $orderItemExtension
     * @param OrderPaymentExtension $orderPaymentExtension
     * @param Manager $eventManager
     * @param OrderPaymentCollectionFactory $orderPaymentCollectionFactory
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        UserContextInterface $userContext,
        SearchResultFactory $searchResultFactory,
        OrderExtension $orderExtension,
        OrderItemExtension $orderItemExtension,
        OrderPaymentExtension $orderPaymentExtension,
        Manager $eventManager,
        OrderPaymentCollectionFactory $orderPaymentCollectionFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->userContext = $userContext;
        $this->searchResultFactory = $searchResultFactory;
        $this->orderItemExtension = $orderItemExtension;
        $this->orderExtension = $orderExtension;
        $this->orderPaymentExtension = $orderPaymentExtension;
        $this->eventManager = $eventManager;
        $this->orderPaymentCollectionFactory = $orderPaymentCollectionFactory;
    }

    /**
     * @param $orderId
     * @return OrderInterface
     * @throws AuthorizationException
     * @throws NoSuchEntityException
     */
    public function getItem($orderId)
    {
        $this->checkCustomerContext();
        $order = $this->orderRepository->get($orderId);
        $this->addOrderExtensionAttributes($order);
        $this->addOrderPaymentExtensionAttributes($order->getPayment());
        $this->addOrderItemExtensionAttributes($order);
        if(!$order->getId() || $order->getCustomerId() !== $this->getCustomerId()) {
            throw new NoSuchEntityException(__('Unable to find order %orderId', ['orderId' => $orderId]));
        }

        return $order;
    }

    /**
     * Get list of customer order items
     *
     * @param SearchCriteria|null $searchCriteria
     * @return OrderSearchResultInterface
     * @throws AuthorizationException
     */
    public function getCustomerOrders(SearchCriteria $searchCriteria = null)
    {
        $this->checkCustomerContext();
        /** @var \Magento\Sales\Api\Data\OrderSearchResultInterface $searchResult */
        $searchResult = $this->searchResultFactory->create();
        $searchResult->addFieldToFilter('customer_id', ['eq' => $this->getCustomerId()]);
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $this->addFilterGroupToCollection($filterGroup, $searchResult);
        }

        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders === null) {
            $sortOrders = [];
        }
        /** @var \Magento\Framework\Api\SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $searchResult->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
            );
        }

        $this->eventManager->dispatch(
            'order_management_customer_orders_before',
            ['search_criteria', $searchCriteria]
        );

        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setCurPage($searchCriteria->getCurrentPage());
        $searchResult->setPageSize($searchCriteria->getPageSize());
        foreach ($searchResult->getItems() as $order) {
            $this->addOrderExtensionAttributes($order);
        }
        return $searchResult;
    }

    /**
     * Get order id from hash generated when asking for paypal express checkout token
     *
     * @param string $paypalHash
     * @return int
     */
    public function getOrderIdFromHash($paypalHash)
    {
        /** @var OrderPaymentCollection $collection */
        $collection = $this->orderPaymentCollectionFactory->create();

        $collection->addFieldToFilter(
            'additional_information',
            ['like' => "%\"paypalExpressHash\":\"{$paypalHash}\"%"]
        );
        /** @var OrderPaymentInterface $orderPayment */
        $orderPayment = $collection->getFirstItem();

        return (int)$orderPayment->getParentId();
    }

    /**
     * Get user id from context
     *
     * @return int|null
     */
    private function getCustomerId()
    {
        return $this->userContext->getUserId();
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param OrderSearchResultInterface $searchResult
     * @return void
     */
    private function addFilterGroupToCollection(
        FilterGroup $filterGroup,
        OrderSearchResultInterface $searchResult
    ) {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $conditions[] = [$condition => $filter->getValue()];
            $fields[] = $filter->getField();
        }

        $transport = new DataObject(['fields' => $fields, 'conditions' => $conditions]);
        $this->eventManager->dispatch(
            'order_management_prepare_filter_group',
            ['filter_group' => $filterGroup, 'data' => $transport]
        );

        $fields = $transport->getFields();
        $conditions = $transport->getConditions();

        if ($fields) {
            $searchResult->addFieldToFilter($fields, $conditions);
        }
    }

    /**
     * Add extension attributes to order entity
     *
     * @param OrderInterface $order
     */
    private function addOrderExtensionAttributes(OrderInterface $order)
    {
        $this->orderExtension->addAttributes($order);
    }

    /**
     * Add extension attributes to order payment
     *
     * @param OrderPaymentInterface $payment
     */
    private function addOrderPaymentExtensionAttributes(OrderPaymentInterface $payment)
    {
        $this->orderPaymentExtension->addAttributes($payment);
    }

    /**
     * Add extension attributes to order items
     *
     * @param OrderInterface $order
     */
    private function addOrderItemExtensionAttributes(OrderInterface $order)
    {
        foreach($order->getItems() as $item) { /** @var OrderItemInterface $item */
            $this->orderItemExtension->addAttributes($item);
        }
    }

    /**
     * Check if current user context is for logged in customer
     *
     * @return bool
     * @throws AuthorizationException
     */
    private function checkCustomerContext()
    {
        if ($this->userContext->getUserType() !== UserContextInterface::USER_TYPE_CUSTOMER) {
            throw new AuthorizationException(__('This method is available only for customer tokens'));
        }

        return true;
    }
}