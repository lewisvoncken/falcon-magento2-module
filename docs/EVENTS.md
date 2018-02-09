# Custom events

* `customer_generate_token_guest_cart_check`
    - fired in `Hatimeria\Reagento\Model\CustomerTokenService::shouldMergeCart` after fetching guest cart
    - params:
        * `guest_cart`: `Magento\Framework\Quote\Api\Data\CartInterface`
        * `cart_to_merge`: `Magento\Framework\DataObject`
            * property `result` contains either guest cart object or false if given cart is already assigned to some customer