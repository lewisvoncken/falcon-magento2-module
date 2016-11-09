# Reagento (Magento 2 module)

Custom API endpoints provided by this module:

- `/rest/V1/url/:url` - get info about cms page by the given URL
- `/rest/V1/info` - get basic settings for current shop
- `/rest/V1/categories/homepage` - get categories marked as "Show on homepage" (max - 6)

## Important settings (if you Magento instance on a separate domain)

- Put Magento2 application to a subdomain of the main domain (for example `api.example.com`)
- Set `cookie domain` config value to the domain domain with a leading dot symbol (for example, `.example.com`)

## Local development of Reagento Module (using HAT >= v1.0.5)

Use `hat li --mounted-packages [path-to-reagento-module]` flag to install the project and
get this module symlinked into your project's dependencies.
