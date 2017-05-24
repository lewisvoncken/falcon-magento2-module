<?php

namespace Hatimeria\Reagento\Model\Plugin;

use Hatimeria\Reagento\Api\UrlInterface;
use Hatimeria\Reagento\Model\Api\AdyenRedirect;
use Hatimeria\Reagento\Model\Api\OrderResponse;
use Magento\Sales\Model\Order;
use Adyen\Payment\Block\Redirect\Validate3d;
use Magento\Sales\Api\OrderRepositoryInterface;

class AfterPlaceOrder
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * AfterPlaceOrder constructor.
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->orderRepository = $orderRepository;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param $subject
     * @param $orderId
     * @return OrderResponse | string
     */
    public function afterPlaceOrder($subject, $orderId)
    {
        /** @var Order $order */
        $order = $this->orderRepository->get($orderId);
        $payment = $order->getPayment();
        // TODO replace the object creation with dependency injection of interface, and make object implement it
        $obj = new OrderResponse();
        $paymentAdditionalInfo = $payment->getAdditionalInformation();
        if ($paymentAdditionalInfo) {
            if ($payment->getMethod() == 'adyen_cc' && isset($paymentAdditionalInfo['3dActive']) && true === $paymentAdditionalInfo['3dActive'] ) {
                // TODO replace the object creation with dependency injection of interface, and make object implement it
                $adyen = new AdyenRedirect();
                $adyen->setIssuerUrl($paymentAdditionalInfo['issuerUrl']);
                $adyen->setMd($paymentAdditionalInfo['md']);
                $adyen->setPaRequest($paymentAdditionalInfo['paRequest']);
                $adyen->setTermUrl($this->_getTermUrl());

                $obj->setAdyen($adyen);
                $obj->setOrderId($order->getData('increment_id'));
                return $obj;
            }
        }
        return $order->getData('increment_id');
    }

    /**
     * @return \Hatimeria\Reagento\Api\Data\UrlDataInterface
     */
    protected function _getTermUrl()
    {
        return $this->urlBuilder->getUrl('adyen/process/validate3d');
    }
}