# Reagento (Magento 2 module)

## Elements introduced to magento by this module

Custom API endpoints provided by this module:

- `[GET] /rest/V1/categories/:categoryId/breadcrumbs` - get category breadcrumbs to the root category
- `[GET] /rest/V1/categories/homepage` - get categories marked as "Show on homepage" (max - 6)
- `[GET] /rest/V1/categories` (**overridden**) - get category tree with `url_path` data
- `[GET] /rest/V1/customer-payment/:customerId/:orderId/adyen-link` - get Adyen payment link for redirection (for registered customer)
- `[GET] /rest/V1/guest-carts/:cartId/paypal-fetch-token` - get PayPal token
- `[GET] /rest/V1/guest-orders/:orderId/order-info` - get data for the order specified by masked id for guest orders
- `[GET] /rest/V1/guest-payment/:cartId/:orderId/adyen-link` - get Adyen payment link for redirection
- `[GET] /rest/V1/info` - get basic settings for current shop
- `[GET] /rest/V1/order-info/:quoteId` - get order information from quote ID (orderId, revenue, shipping, tax etc)
- `[GET] /rest/V1/orders/:orderId/order-info` - get data for the order specified by order id for logged in customer
- `[GET] /rest/V1/orders/mine` - get list of logged customer orders
- `[GET] /rest/V1/products` (**overridden**) - get product list with a custom `filters` data in response
- `[GET] /rest/V1/url/?requestPath=:url` - get info about cms page by the given URL
- `[POST] /rest/V1/carts/mine/payment-information` (**overridden**) - modifies the docblock of function return type (may be int or OrderResponse object)
- `[POST] /rest/V1/contact` - send a contact email
- `[POST] /rest/V1/guest-carts/:cartId/payment-information` (**overridden**) - modifies the docblock of function return type (may be int or OrderResponse object)
- `[PUT] /rest/V1/carts/mine/reagento-order` - place order with Adyen credit card as a logged in customer - getting an object as a response
- `[PUT] /rest/V1/guest-carts/:cartId/reagento-order` - place order with Adyen credit card - getting an object as a response
- `[GET] /rest/V1/menu` - get menu tree

Extension attributes:

- `Magento\Catalog\Api\Data\ProductInterface`:
    ```json
    {
      "thumbnail_resized_url": "string",
      "thumbnail_url": "string",
      "media_gallery_sizes": "Hatimeria\Reagento\Api\Data\GalleryMediaEntrySizeInterface[]",
      "breadcrumbs": "Hatimeria\Reagento\Api\Data\BreadcrumbInterface[]"
    }
    ```
- `Magento\Quote\Api\Data\TotalsItemInterface`:
    ```json
    {
      "thumbnail_url": "string",
      "url_key": "string",
      "available_qty": "string"
    }
    ```
- `Magento\Catalog\Api\Data\CategoryInterface`:
    ```json
    {
      "breadcrumbs": "Hatimeria\Reagento\Api\Data\BreadcrumbInterface[]"
    }
    ```
- `Magento\Store\Api\Data\StoreConfigInterface`:
    ```json
    {
      "optional_post_codes": "mixed"
    }
    ```
- `Magento\Store\Api\Data\StoreInterface`:
    ```json
    {
      "is_active": "int"
    }
    ```
- `Magento\Sales\Api\Data\OrderInterface`:
    ```json
    {
      "masked_id": "string"
    }
    ```

Custom changes:

- Price for Configurable products (`$product->setPriceCalculation(false)`)

Custom commands:

- `rapidflow:attribute:options [-s|--shop="..."] [--attribute_codes="..."]` - Generate import file for option label translation
