<?php

namespace Hatimeria\Reagento\Controller\Adyen;

use \Adyen\Payment\Controller\Process\Validate3d as AdyenValidate3d;

class Validate3d extends AdyenValidate3d
{
    /**
     * Get order object
     *
     * @return \Magento\Sales\Model\Order
     * @throws \Exception
     */
    protected function _getOrder()
    {
        if (!$this->_order) {
            $incrementId = $this->getRequest()->getPost('order_id');
            if (!$incrementId) {
                throw new \Exception('Order ID is empty');
            }
            $this->_adyenLogger->addAdyenResult('order id is #' . $incrementId);
            $this->_orderFactory = $this->_objectManager->get('Magento\Sales\Model\OrderFactory');
            $this->_order = $this->_orderFactory->create()->loadByIncrementId($incrementId);
        }
        return $this->_order;
    }
}
