<?php
namespace Deity\MagentoApi\Plugin\Customer\Api;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerExtensionInterface;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Newsletter\Model\SubscriberFactory;

class CustomerRepository
{
    /** @var SubscriberFactory */
    private $subscriberFactory;

    /** @var ExtensionAttributesFactory */
    private $extensionAttributesFactory;

    /**
     * @param SubscriberFactory $subscriberFactory
     * @param ExtensionAttributesFactory $extensionAttributesFactory
     */
    public function __construct(
        SubscriberFactory $subscriberFactory,
        ExtensionAttributesFactory $extensionAttributesFactory
    ) {
        $this->subscriberFactory = $subscriberFactory;
        $this->extensionAttributesFactory = $extensionAttributesFactory;
    }

    /**
     * Add extension attributes to customer entity
     *
     * @param CustomerRepositoryInterface $subject
     * @param CustomerInterface $result
     * @return CustomerInterface
     */
    public function afterGetById(CustomerRepositoryInterface $subject, CustomerInterface $result)
    {
        $extensionAttributes = $this->getExtensionAttributes($result);
        $result->setExtensionAttributes($extensionAttributes);

        $this->addNewsletterAttributes($result);

        return $result;
    }

    /**
     * Get extension attributes object or create one if nothing has yet been set
     *
     * @param CustomerInterface $customerData
     * @return CustomerExtensionInterface
     */
    private function getExtensionAttributes(CustomerInterface $customerData)
    {
        $extensionAttributes = $customerData->getExtensionAttributes();
        if (!$extensionAttributes) {
            $extensionAttributes = $this->extensionAttributesFactory->create(CustomerInterface::class);
        }

        return $extensionAttributes;
    }

    /**
     * Set newsletter related extension attributes
     *
     * @param CustomerInterface $customer
     */
    private function addNewsletterAttributes(CustomerInterface$customer) {
        $subscriber = $this->subscriberFactory->create();
        $subscriber->loadByEmail($customer->getEmail());
        $customer->getExtensionAttributes()->setNewsletterSubscriber($subscriber->isSubscribed());
    }
}