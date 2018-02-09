# Custom events

* `order_management_customer_orders_before`
    - fired in `Hatimeria\Reagento\Model\OrderManagement::getCustomerOrders` before fetching customer orders from the database
    - params: 
        * `search_criteria`: `Magento\Framework\Api\SearchCriteriaInterface`

* `order_management_prepare_filter_group`
    - fired in `Hatimeria\Reagento\Model\OrderManagement::addFilterGroupToCollection` after initially processing filter group data but before passing them to the search criteria object
    - params:
        * `filter_group`: `Magento\Framework\Api\Search\FilterGroup` - currently processed filter group 
        * `data`: `Magento\Framework\DataObject`
            * `fields` - an array with fields defined in the group
            * `conditions` - an array with fields condition to filter
             