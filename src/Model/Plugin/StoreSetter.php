<?php
/**
 * Created by PhpStorm.
 * User: wbrodsky
 * Date: 02/06/2017
 * Time: 09:59
 */

namespace Hatimeria\Reagento\Model\Plugin;

use Magento\Store\Model\StoreManagerInterface;

class StoreSetter
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;


    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * @param $subject
     * @param \Magento\Framework\App\Action\AbstractAction $actionInstance
     */
    public function afterMatch($subject, $actionInstance)
    {
        $request = $actionInstance->getRequest();
        // Currently apply only for paypal controller in Hatimeria extension, remove this condition when needed elsewhere
        if (
            'payment_paypal_express' == $request->getControllerName() &&
            'checkoutExt' == $request->getModuleName()
        ) {
            $storeId = $request->getParam('store_id');
            if ($storeId) {
                $this->storeManager->setCurrentStore($storeId);
            }
        }

        return $actionInstance;
    }
}