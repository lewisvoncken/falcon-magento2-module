# Reagento (Magento 2 module)

Custom API endpoints provided by this module:

- `[POST] /rest/V1/contact` - send a contact email
- `[GET] /rest/V1/url/?requestPath=:url` - get info about cms page by the given URL
- `[GET] /rest/V1/info` - get basic settings for current shop
- `[GET] /rest/V1/categories/homepage` - get categories marked as "Show on homepage" (max - 6)
- `[GET] /rest/V1/categories/:categoryId/breadcrumbs` - get category breadcrumbs to the root category
- `[GET] /rest/V1/categories` (**overridden**) - get category tree with `url_path` data
- `[GET] /rest/V1/products` (**overridden**) - get product list with a custom `filters` data in response
- `[GET] /rest/V1/guest-carts/:cartId/paypal-fetch-token` - get PayPal token
- `[GET] /rest/V1/order-info/:quoteId` - get order information from quote ID (orderId, revenue, shipping, tax etc)
- `[GET] /rest/V1/guest-payment/:cartId/:orderId/adyen-link` - get Adyen payment link for redirection
- `[GET] /rest/V1/customer-payment/:customerId/:orderId/adyen-link` - get Adyen payment link for redirection (for registered customer)

Extension attributes:

- `Magento\Catalog\Api\Data\ProductInterface`:
    - `<attribute code="thumbnail_resized_url" type="string" />`
    - `<attribute code="thumbnail_url" type="string" />`
    - `<attribute code="media_gallery_sizes" type="Hatimeria\Reagento\Api\Data\GalleryMediaEntrySizeInterface[]" />`
- `Magento\Quote\Api\Data\TotalsItemInterface`:
    - `<attribute code="thumbnail_url" type="string" />`
    - `<attribute code="url_key" type="string" />`
    - `<attribute code="available_qty" type="string" />`
- `Magento\Catalog\Api\Data\CategoryInterface`:
    - `<attribute code="breadcrumbs" type="mixed"/>`

Custom changes:

- Price for Configurable products (`$product->setPriceCalculation(false)`)

## Creating package releases

In order to create package release, create GIT tag and push it all together to the repository - you have
to run the following command:

```
composer run-script release
```

It will raise the package version in `composer.json` and `etc/module.xml` files, create a GIT tag and push these changes
to the repository.

By default - `patch` version will be raised. If you want to raise major or minor - set it with the following syntax:

```
composer run-script release -- minor
```

## Using latest release of this package

In order to use the latest package version - you have to set package version with the following rules:

- `0.*` - use the latest possible release up to `1.0.0` version
- `0.2.*` - use the latest possible release up to `0.3.0` version

## Important settings (if your Magento instance is on a separate domain)

- Put Magento2 application to a subdomain of the main domain (for example `api.example.com`)
- Set `cookie domain` config value to the domain domain with a leading dot symbol (for example, `.example.com`)

## Local development of Reagento Module (using HAT >= v1.0.5)

Use `hat li --mounted-packages [path-to-reagento-module]` flag to install the project and
get this module symlinked into your project's dependencies.

## Custom product attributes in totals API endpoint

To add product attributes per cart item use di.xml to inject your list e.g.

```
<type name="Hatimeria\Reagento\Model\Cart\Item\AttributeList">
    <arguments>
        <argument name="attributes" xsi:type="array">
            <item name="brand" xsi:type="string">brand</item>
        </argument>
    </arguments>
</type>
```
