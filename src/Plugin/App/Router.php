<?php

namespace Deity\MagentoApi\Plugin\App;

use Magento\Framework\App\Action\AbstractAction;
use Magento\Framework\App\RouterInterface;
use Magento\Store\Model\StoreManagerInterface;

class Router
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * StoreSetter constructor.
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * @param RouterInterface $subject
     * @param AbstractAction|array $actionInstance
     * @return AbstractAction
     */
    public function afterMatch(RouterInterface $subject, $actionInstance)
    {
        if ($actionInstance && $actionInstance instanceof AbstractAction) {
            $request = $actionInstance->getRequest();
            //Currently apply only for paypal controller in Deity_MagentoApi extension, remove this condition when needed elsewhere
            if (
                'payment_paypal_express' == $request->getControllerName() &&
                'checkoutExt' == $request->getModuleName()
            ) {
                $storeId = $request->getParam('store_id');
                if ($storeId) {
                    $this->storeManager->setCurrentStore($storeId);
                }
            }

        }
        return $actionInstance;
    }
}