<?php

namespace Hatimeria\Reagento\Plugin\Quote\Model;

use Hatimeria\Reagento\Model\Api\AdyenRedirect;
use Hatimeria\Reagento\Model\Api\AdyenRedirectFactory;
use Hatimeria\Reagento\Model\Api\OrderResponse;
use Magento\Framework\UrlInterface as MagentoUrlInterface;
use Magento\Quote\Model\QuoteManagement as MagentoQuoteManagement;
use Magento\Sales\Model\Order;
use Magento\Sales\Api\OrderRepositoryInterface;

class QuoteManagement
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var MagentoUrlInterface
     */
    protected $urlBuilder;

    /** @var AdyenRedirectFactory */
    protected $adyenRedirectFactory;

    /**
     * AfterPlaceOrder constructor.
     * @param OrderRepositoryInterface $orderRepository
     * @param MagentoUrlInterface $urlBuilder
     * @param AdyenRedirectFactory $adyenRedirectFactory
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        MagentoUrlInterface $urlBuilder,
        AdyenRedirectFactory $adyenRedirectFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->urlBuilder = $urlBuilder;
        $this->adyenRedirectFactory = $adyenRedirectFactory;
    }

    /**
     * @param MagentoQuoteManagement $subject
     * @param int $orderId
     * @return OrderResponse | string
     */
    public function afterPlaceOrder(MagentoQuoteManagement $subject, $orderId)
    {
        /** @var Order $order */
        $order = $this->orderRepository->get($orderId);
        $payment = $order->getPayment();
        // TODO replace the object creation with dependency injection of interface, and make object implement it
        $obj = new OrderResponse();
        $paymentAdditionalInfo = $payment->getAdditionalInformation();
        if ($paymentAdditionalInfo) {
            if ($payment->getMethod() == 'adyen_cc' && isset($paymentAdditionalInfo['3dActive']) && true === $paymentAdditionalInfo['3dActive'] ) {
                // TODO replace the object factory with interface factory
                /** @var AdyenRedirect $adyen */
                $adyen = $this->adyenRedirectFactory->create();
                $adyen->setIssuerUrl($paymentAdditionalInfo['issuerUrl']);
                $adyen->setMd($paymentAdditionalInfo['md']);
                $adyen->setPaRequest($paymentAdditionalInfo['paRequest']);
                $adyen->setTermUrl($this->_getTermUrl());

                $obj->setAdyen($adyen);
            }
        }
        $obj->setOrderId($orderId);

        return $obj;
    }

    /**
     * @return string
     */
    protected function _getTermUrl()
    {
        return $this->urlBuilder->getUrl('adyen/process/validate3d');
    }
}
