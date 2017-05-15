<?php

namespace Hatimeria\Reagento\Model\Plugin;

use Hatimeria\Reagento\Api\UrlInterface;
use Magento\Sales\Model\Order;
use Adyen\Payment\Block\Redirect\Validate3d;
use Magento\Sales\Api\OrderRepositoryInterface;

class AfterPlaceOrder
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

//    /**
//     * @var UrlInterface
//     */
//    protected $urlBuilder;

    /**
     * AfterPlaceOrder constructor.
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository//,
        //\Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->orderRepository      = $orderRepository;
//        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param $subject
     * @param $orderId
     * @return int|array
     */
    public function afterPlaceOrder($subject, $orderId)
    {
        /** @var Order $order */
        $order = $this->orderRepository->get($orderId);
        $payment = $order->getPayment();
        if ($payment->getAdditionalInformation()) {
            $paymentAdditionalInfo = $payment->getAdditionalInformation();
            if ( isset($paymentAdditionalInfo['3dActive']) && true === $paymentAdditionalInfo['3dActive'] ) {
                $result = [
                    'issuerUrl' => $paymentAdditionalInfo['issuerUrl'],
//                    'termUrl'  => $this->_getTermUrl(),
                    'md' => $paymentAdditionalInfo['md'],
                    'paRequest' => $paymentAdditionalInfo['paRequest']
                ];

                return json_encode($result);
            }
        }

        return $order->getData('increment_id');
    }

//    /**
//     * @return \Hatimeria\Reagento\Api\Data\UrlDataInterface
//     */
//    protected function _getTermUrl()
//    {
//        return $this->urlBuilder->getUrl('adyen/process/validate3d');
//    }
}