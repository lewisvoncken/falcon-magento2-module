<?php

namespace Deity\MagentoApi\Plugin\ExtensionAttribute;

use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\Api\ExtensionAttributesFactory;

class JoinProcessor
{
    /** @var ExtensionAttributesFactory */
    private $extensionAttributesFactory;

    /**
     * JoinProcessor constructor.
     * @param ExtensionAttributesFactory $extensionAttributesFactory
     */
    public function __construct(ExtensionAttributesFactory $extensionAttributesFactory)
    {
        $this->extensionAttributesFactory = $extensionAttributesFactory;
    }

    /**
     * @param JoinProcessorInterface $subject
     * @param callable $proceed
     * @param string $extensibleEntityClass
     * @param array $data
     * @return array
     */
    public function aroundExtractExtensionAttributes(JoinProcessorInterface $subject, callable $proceed, $extensibleEntityClass, array $data)
    {
        $result = $proceed($extensibleEntityClass, $data);
        if (array_key_exists(ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY, $result)) {
            $extensionAttributes = $result[ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY];
            $extensionAttributeClass = $this->extensionAttributesFactory->getExtensibleInterfaceName($extensibleEntityClass);
            if (!is_subclass_of($extensionAttributes, $extensionAttributeClass)) {
                unset($result[ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY]);
            }
        }
        return $result;
    }

}