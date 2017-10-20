#This file contains information about the process of internal module development.

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