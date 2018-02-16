<?php
namespace Deity\MagentoApi\Model\Sales\Order\Payment;

use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Api\Data\OrderPaymentExtensionInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Store\Model\ScopeInterface;

class Extension
{
    /** @var ExtensionAttributesFactory */
    private $extensionAttributesFactory;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /**
     * Extension constructor.
     * @param ExtensionAttributesFactory $extensionAttributesFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ExtensionAttributesFactory $extensionAttributesFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->extensionAttributesFactory = $extensionAttributesFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Add extension attributes to order item extension
     *
     * @param Payment|OrderPaymentInterface $orderPayment
     */
    public function addAttributes(OrderPaymentInterface $orderPayment)
    {
        $extensionAttributes = $this->getOrderPaymentExtensionAttribute($orderPayment);
        $extensionAttributes->setMethodName(
            $this->scopeConfig->getValue("payment/{$orderPayment->getMethod()}/title"),
            ScopeInterface::SCOPE_STORES,
            $orderPayment->getOrder() ? $orderPayment->getOrder()->getStoreId() : null
        );

        $orderPayment->setExtensionAttributes($extensionAttributes);
    }

    /**
     * @param OrderPaymentInterface $orderPayment
     * @return OrderPaymentExtensionInterface
     */
    private function getOrderPaymentExtensionAttribute(OrderPaymentInterface $orderPayment)
    {
        $extensionAttributes = $orderPayment->getExtensionAttributes();
        if (!$extensionAttributes) {
            $extensionAttributes = $this->extensionAttributesFactory->create(OrderPaymentInterface::class);
        }

        return $extensionAttributes;
    }
}