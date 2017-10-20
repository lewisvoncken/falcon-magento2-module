# Reagento module coding standard

## PHP CODING STANDARDS

List of `PSR` rules used during the module development:

- PSR-1: http://www.php-fig.org/psr/psr-1/
- PSR-2: http://www.php-fig.org/psr/psr-2/
- PSR-3: http://www.php-fig.org/psr/psr-3/ for logging use `\Psr\LoggerInterface` as it is dependency of magento platform; 
for more information regarding creating custom log file see `LOGGING.md`
- PSR-4: http://www.php-fig.org/psr/psr-4/ avoid adding PSR-0 written libraries

## SPECIFIC RULES REGARDING MAGENTO RELATED CODE

### Versioning

- Schema of the versioning number is as follows: `[magento-minor].[module-minor].[module-update]`. 
In other words major of the module defines compatibility with the minor release of the Magento platform, ie.
`1.x.x` is a line for Magento 2.1.x line
`2.x.x` is a line for Magento 2.2.x line
- `[module-minor]` defines possible backward incompatible changes
- `[module-update]` defines backward compatible changes
- Any change to `@api` interface or class that adds new methods and/or fields does not constitute backward compatibility break
- Only definition changes of public methods and properties in `@api` interfaces and classes constitute backward compatibility break  
- Make sure magento internal module version in `etc/module.xml` matches version set in `composer.json` file
- There is no official support for Magento 2.0.x line
- Each new release should contain an entry in `CHANGELOG.md` file with short description of the changes


### Scope of methods and properties

- Use `public` only on methods and properties that are meant to be accessed from client side
- Avoid defining public properties, create setter/getter class for them 
- Avoid using `private` methods and properties, if there is no reason for it
- `protected` methods and properties are regarded as internal to the code and are not part of backward compatibility check (this include not only function definition but existence of such function itself)

### Plugins

- Plugins should be kept in `Plugins` directory
- Name of the class should correspond to the class the plugin is hooking into not the method it was meant to intercept when creating it for the first time
- Only intercepted methods should be exposed as `public` methods in the plugin class

### Api
- For magento api classes prefer `extension_attributes` over creating custom endpoints
- For magento api endpoints that does not offer `extension_attributes` prefer creating custom endpoint over overriding magento one