# Custom events

* `customer_generate_token_guest_cart_check`
    - fired in `Deity\MagentoApi\Model\CustomerTokenService::shouldMergeCart` after fetching guest cart
    - params:
        * `guest_cart`: `Magento\Framework\Quote\Api\Data\CartInterface`
        * `cart_to_merge`: `Magento\Framework\DataObject`
            * property `result` contains either guest cart object or false if given cart is already assigned to some customer

* `order_management_customer_orders_before`
    - fired in `Deity\MagentoApi\Model\OrderManagement::getCustomerOrders` before fetching customer orders from the database
    - params: 
        * `search_criteria`: `Magento\Framework\Api\SearchCriteriaInterface`

* `order_management_prepare_filter_group`
    - fired in `Deity\MagentoApi\Model\OrderManagement::addFilterGroupToCollection` after initially processing filter group data but before passing them to the search criteria object
    - params:
        * `filter_group`: `Magento\Framework\Api\Search\FilterGroup` - currently processed filter group 
        * `data`: `Magento\Framework\DataObject`
            * `fields` - an array with fields defined in the group
            * `conditions` - an array with fields condition to filter
