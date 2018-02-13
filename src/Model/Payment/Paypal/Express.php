<?php

namespace Deity\MagentoApi\Model\Payment\Paypal;

use Magento\Paypal\Model\Express as PaypalExpress;
use Magento\Paypal\Model\Express\Checkout as ExpressCheckout;

class Express extends PaypalExpress
{
    /**
     * Place an order with authorization or capture action
     *
     * @param \Magento\Sales\Model\Order\Payment $payment
     * @param float $amount
     * @return $this
     */
    protected function _placeOrder(\Magento\Sales\Model\Order\Payment $payment, $amount)
    {
        $order = $payment->getOrder();

        // prepare api call
        $token = $payment->getAdditionalInformation(ExpressCheckout::PAYMENT_INFO_TRANSPORT_TOKEN);

        $cart = $this->_cartFactory->create(['salesModel' => $order]);

        $api = $this->getApi()->setToken(
            $token
        )->setPayerId(
            $payment->getAdditionalInformation(ExpressCheckout::PAYMENT_INFO_TRANSPORT_PAYER_ID)
        )->setAmount(
            $amount
        )->setPaymentAction(
            $this->_pro->getConfig()->getValue('paymentAction')
        )->setNotifyUrl(
            $this->_urlBuilder->getUrl('paypal/ipn/')
        )->setInvNum(
            $order->getIncrementId()
    // START OF OVERWRITTEN CODE
        )->setCurrencyCode(
            $order->getOrderCurrencyCode()
    // END OF OVERWRITTEN CODE
        )->setPaypalCart(
            $cart
        )->setIsLineItemsEnabled(
            $this->_pro->getConfig()->getValue('lineItemsEnabled')
        );
        if ($order->getIsVirtual()) {
            $api->setAddress($order->getBillingAddress())->setSuppressShipping(true);
        } else {
            $api->setAddress($order->getShippingAddress());
            $api->setBillingAddress($order->getBillingAddress());
        }

        // call api and get details from it
        $api->callDoExpressCheckoutPayment();

        $this->_importToPayment($api, $payment);
        return $this;
    }
}
