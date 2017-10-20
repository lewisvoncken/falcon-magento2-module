# This file describe how to make a custom log file in the module 

This tutorial is a copy of https://magento.stackexchange.com/a/75954/2721

Assuming your module is in `YourNamespace/YourModule`:

1. Write Logger class in `Logger/Logger.php`:
    ```php
    <?php
    namespace YourNamespace\YourModule\Logger;
    
    class Logger extends \Monolog\Logger
    {
    }
    ```

2. Write Handler class in `Logger/Handler.php`:
    ```php
    <?php
    namespace YourNamespace\YourModule\Logger;

    use Monolog\Logger;

    class Handler extends \Magento\Framework\Logger\Handler\Base
    {
        /**
         * Logging level
         * @var int
         */
        protected $loggerType = Logger::INFO;

        /**
         * File name
         * @var string
         */
        protected $fileName = '/var/log/myfilename.log';
    }
    ```

    Note: This is the only step which uses Magento code. `\Magento\Framework\Logger\Handler\Base` extends Monolog's `StreamHandler` and e.g. prepends the $fileName attribute with the Magento base path.

3. Register Logger in Dependency Injection `etc/di.xml`:

    ```xml
    <?xml version="1.0"?>
    <config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
        xsi:noNamespaceSchemaLocation="../../../../../lib/internal/Magento/Framework/ObjectManager/etc/config.xsd">
        <type name="YourNamespace\YourModule\Logger\Handler">
            <arguments>
                <argument name="filesystem" xsi:type="object">Magento\Framework\Filesystem\Driver\File</argument>
            </arguments>
        </type>
        <type name="YourNamespace\YourModule\Logger\Logger">
            <arguments>
                <argument name="name" xsi:type="string">myLoggerName</argument>
                <argument name="handlers"  xsi:type="array">
                    <item name="system" xsi:type="object">YourNamespace\YourModule\Logger\Handler</item>
                </argument>
            </arguments>
        </type>
    </config>
    ```

    Note: This is not strictly required but allows the DI to pass specific arguments to the constructor. If you do not do this step, then you need to adjust the constructor to set the handler.

4. Use the logger in your Magento classes:

    This is done by Dependency Injection. Below you will find a dummy class which only writes a log entry:

    ```php
    <?php
    namespace YourNamespace\YourModule\Model;

    class MyModel
    {
        /**
         * Logging instance
         * @var \YourNamespace\YourModule\Logger\Logger
         */
        protected $_logger;
        
        /**
         * Constructor
         * @param \YourNamespace\YourModule\Logger\Logger $logger
         */
        public function __construct(
            \YourNamespace\YourModule\Logger\Logger $logger
        ) {
            $this->_logger = $logger;
        }
        
        public function doSomething()
        {
            $this->_logger->info('I did something');
        }
    }
    ```