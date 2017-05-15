<?php

namespace Hatimeria\Reagento\Model\Plugin;

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
     * AfterPlaceOrder constructor.
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRepository      = $orderRepository;
    }

    /**
     * @param $subject
     * @param $orderId
     * @return mixed
     */
    public function afterPlaceOrder($subject, $orderId)
    {
        /** @var Order $order */
        $order = $this->orderRepository->get($orderId);
        $payment = $order->getPayment();
        if ($payment->getAdditionalInformation()) {
            $paymentAdditionalInfo = $payment->getAdditionalInformation();
            if ( isset($paymentAdditionalInfo['3dActive']) && true === $paymentAdditionalInfo['3dActive'] ) {
                $result = new \StdClass();
                $result->issuerUrl = $paymentAdditionalInfo['issuerUrl'];
                $result->md        = $paymentAdditionalInfo['md'];
                $result->paRequest = $paymentAdditionalInfo['paRequest'];
//                    'issuerUrl' => $paymentAdditionalInfo['issuerUrl'],
//                    'termUrl'  => $this->adyenValidate3dBlock->getTermUrl(),
//                    'md' => $paymentAdditionalInfo['md'],
//                    'paRequest' => $paymentAdditionalInfo['paRequest']
//                ];

                return $result;
            }
        }

        return $order->getData('increment_id');
    }
}