<?php
namespace Deity\MagentoApi\Model\Payment\Paypal;

use Magento\Paypal\Model\Pro as PaypalPro;

class Pro extends PaypalPro
{
    /**
     * Refund a capture transaction
     *
     * @param \Magento\Framework\DataObject $payment
     * @param float $amount
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @overwritten To set the refund currency equal to order currency, not to base currency
     */
    public function refund(\Magento\Framework\DataObject $payment, $amount)
    {
        $captureTxnId = $this->_getParentTransactionId($payment);
        if ($captureTxnId) {
            $api = $this->getApi();
            $order = $payment->getOrder();
            $api->setPayment(
                $payment
            )->setTransactionId(
                $captureTxnId
            )->setAmount(
                $amount
        //START OF OVERWRITTEN CODE
            )->setCurrencyCode(
                $order->getOrderCurrencyCode()
        //END OF OVERWRITTEN CODE
            );
            $canRefundMore = $payment->getCreditmemo()->getInvoice()->canRefund();
            $isFullRefund = !$canRefundMore &&
                0 == (double)$order->getBaseTotalOnlineRefunded() + (double)$order->getBaseTotalOfflineRefunded();
            $api->setRefundType(
                $isFullRefund ? \Magento\Paypal\Model\Config::REFUND_TYPE_FULL : \Magento\Paypal\Model\Config::REFUND_TYPE_PARTIAL
            );
            $api->callRefundTransaction();
            $this->_importRefundResultToPayment($api, $payment, $canRefundMore);
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We can\'t issue a refund transaction because there is no capture transaction.')
            );
        }
    }
}