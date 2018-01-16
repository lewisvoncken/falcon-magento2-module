#Modules not required or supported

When building reagento-powered app with Magento as shop backend not all magento modules are required. 
The following list documents which modules are either not supported by reagento frontend yet or are not required and may be disabled.

- If standard import and export feature is not used this modules can be safely disabled
    * `Magento_AdvancedPricingImportExport`
    * `Magento_BundleImportExport`
    * `Magento_ConfigurableImportExport`
    * `Magento_CustomerImportExport`
    * `Magento_DownloadableImportExport`
    * `Magento_GroupedImportExport`
    
- Any shipping carrier modules that is not used may be safely disable
    * `Magento_Dhl`
    * `Magento_Fedex`
    * `Magento_Ups`
    * `Magento_Usps`

- Not supported payment methods that can be disabled:
    * `Magento_Braintree`
    * `Magento_Authorizenet`
    
- Other not supported or unused modules:
    * `Magento_Cookie`
    * `Magento_GoogleOptimizer`
    * `Magento_GoogleAdwords`
    * `Magento_GoogleAnalytics`
    * `Magento_LayeredNavigation`
    * `Magento_Marketplace`
    * `Magento_Multishipping`
    * `Magento_Persistent`
    * `Magento_SendFriend`
    * `Magento_Swagger`
    * `Shopial_Facebook`
    
The following modules require rises error when disabling with requirements check. We have not noticed and problems with those modules disabled.

- Not supported modules:
    * `Magento_CatalogSearch`
    * `Magento_Downloadable` (make sure you do not have those products created, by disabling this module you will not be able to manage them in admin)
    * `Magento_GiftMessage`
    * `Magento_GroupedProduct` (make sure you do not have those products created, by disabling this module you will not be able to manage them in admin)
    * `Magento_Newsletter`
    * `Magento_ProductAlert`
    * `Magento_Review`
    * `Magento_Rss`
