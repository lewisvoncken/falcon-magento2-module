<?php

namespace Hatimeria\Reagento\Plugin\Quote\Model;

use Hatimeria\Reagento\Api\Data\AdyenRedirectInterface;
use Hatimeria\Reagento\Api\Data\AdyenRedirectInterfaceFactory;
use Hatimeria\Reagento\Api\Data\OrderResponseInterface;
use Hatimeria\Reagento\Api\Data\OrderResponseInterfaceFactory;
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

    /** @var AdyenRedirectInterfaceFactory */
    protected $adyenRedirectFactory;

    /** @var OrderResponseInterfaceFactory */
    protected $orderResponseFactory;

    /**
     * AfterPlaceOrder constructor.
     * @param OrderRepositoryInterface $orderRepository
     * @param MagentoUrlInterface $urlBuilder
     * @param AdyenRedirectInterfaceFactory $adyenRedirectFactory
     * @param OrderResponseInterfaceFactory $orderResponseFactory
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        MagentoUrlInterface $urlBuilder,
        AdyenRedirectInterfaceFactory $adyenRedirectFactory,
        OrderResponseInterfaceFactory $orderResponseFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->urlBuilder = $urlBuilder;
        $this->adyenRedirectFactory = $adyenRedirectFactory;
        $this->orderResponseFactory = $orderResponseFactory;
    }

    /**
     * @param MagentoQuoteManagement $subject
     * @param int $orderId
     * @return OrderResponseInterface | string
     */
    public function afterPlaceOrder(MagentoQuoteManagement $subject, $orderId)
    {
        /** @var Order $order */
        $order = $this->orderRepository->get($orderId);
        $payment = $order->getPayment();

        /** @var OrderResponseInterface $obj */
        $obj = $this->orderResponseFactory->create();
        $paymentAdditionalInfo = $payment->getAdditionalInformation();
        if ($paymentAdditionalInfo) {
            if (
                $payment->getMethod() == 'adyen_cc'
                && isset($paymentAdditionalInfo['3dActive'])
                && true === $paymentAdditionalInfo['3dActive']
            ) {
                /** @var AdyenRedirectInterface $adyen */
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
