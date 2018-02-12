<?php
namespace Deity\MagentoApi\Observer\Catalog\Product;


use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;

class CheckWebsiteAvailability implements ObserverInterface
{
    /** @var StoreManagerInterface */
    protected $storeManager;

    /**
     * CheckWebsiteAvailability constructor.
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    public function execute(Observer $observer)
    {
        /** @var Product|ProductInterface $product */
        $product = $observer->getEvent()->getProduct();

        if (!in_array($this->storeManager->getStore()->getWebsiteId(), $product->getWebsiteIds())) {
            $observer->getEvent()->getSalable()->setData('is_salable', 0);
        }
    }
}