<?php

namespace Hatimeria\Reagento\Controller\Adyen;

use \Adyen\Payment\Controller\Process\Validate3d as AdyenValidate3d;
use Magento\Quote\Model\QuoteIdMaskFactory;

class Validate3d extends AdyenValidate3d
{
    /**
     * @param \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;
    /**
     * @param QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;
    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Adyen\Payment\Logger\AdyenLogger $adyenLogger,
        \Adyen\Payment\Helper\Data $adyenHelper,
        \Adyen\Payment\Model\Api\PaymentRequest $paymentRequest,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Quote\Model\QuoteFactory $quoteFactory
    ) {
        parent::__construct(
            $context,
            $adyenLogger,
            $adyenHelper,
            $paymentRequest,
            $orderRepository
        );
        $this->jsonHelper         = $jsonHelper;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->_eventManager      = $eventManager;
        $this->quoteFactory       = $quoteFactory;
    }
    
    /**
     * Method overite to handle json response for reagento requests
     */
    public function execute()
    {
        $userAgent = $this->getRequest()->getServer('HTTP_USER_AGENT');
        // proceed to standard behaviour if request is not from Reagento
        if (false === strpos($userAgent, 'Reagento')) {

            return parent::execute();
        }

        $active = null;

        // check if 3d is active
        $order = $this->_getOrder();

        if ($order->getPayment()) {
            $active = $order->getPayment()->getAdditionalInformation('3dActive');
        }

        // check if 3D secure is active. If not just go to success page
        if ($active) {

            $this->_adyenLogger->addAdyenResult("3D secure is active");

            // check if it is already processed
            if ($this->getRequest()->isPost()) {

                $this->_adyenLogger->addAdyenResult("Process 3D secure payment");
                $requestMD = $this->getRequest()->getPost('MD');
                $requestPaRes = $this->getRequest()->getPost('PaRes');
                $md = $order->getPayment()->getAdditionalInformation('md');

                if ($requestMD == $md) {

                    $order->getPayment()->setAdditionalInformation('paResponse', $requestPaRes);

                    try {
                        $result = $this->_authorise3d($order->getPayment());
                    } catch (\Exception $e) {
                        $this->_adyenLogger->addAdyenResult("Process 3D secure payment was refused");
                        $result = 'Refused';
                    }

                    $this->_adyenLogger->addAdyenResult("Process 3D secure payment result is: " . $result);

                    // check if authorise3d was successful
                    if ($result == 'Authorised') {
                        $order->addStatusHistoryComment(__('3D-secure validation was successful'))->save();
                        // set back to false so when pressed back button on the success page it will reactivate 3D secure
                        $order->getPayment()->setAdditionalInformation('3dActive', '');
                        $this->_orderRepository->save($order);

                        // switched original redirect to json response
                        $this->getResponse()->representJson(
                            $this->jsonHelper->jsonEncode([
                                'success' => true
                            ])
                        );
                    } else {
                        $order->addStatusHistoryComment(__('3D-secure validation was unsuccessful.'))->save();

                        // Move the order from PAYMENT_REVIEW to NEW, so that can be cancelled
                        $order->setState(\Magento\Sales\Model\Order::STATE_NEW);
                        $this->_adyenHelper->cancelOrder($order);
                        $this->messageManager->addErrorMessage("3D-secure validation was unsuccessful");
                        
                        // restore the quote
                        $this->restoreQuote($order);

                        // generate masked quote id for failed validation to reload quote in reagento
                        $quoteId = $order->getQuoteId();
                        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($quoteId, 'quote_id');
                        if ($quoteIdMask->getMaskedId() === null) {
                            $quoteIdMask->setQuoteId($quoteId)->save();
                        }
                        // switched original redirect to json response
                        $this->getResponse()->representJson(
                            $this->jsonHelper->jsonEncode([
                                'success' => false,
                                'quoteId' => $quoteIdMask->getMaskedId()
                            ])
                        );
                    }
                }
            } else {
                $this->_adyenLogger->addAdyenResult("Customer was redirected to bank for 3D-secure validation.");
                $order->addStatusHistoryComment(
                    __('Customer was redirected to bank for 3D-secure validation.')
                )->save();

                $this->_view->loadLayout();
                $this->_view->getLayout()->initMessages();
                $this->_view->renderLayout();
            }
        } else {
            // switched original redirect to json response
            $this->getResponse()->representJson(
                $this->jsonHelper->jsonEncode([
                    'success' => true
                ])
            );
        }
    }
    /**
     * Get order object
     *
     * @return \Magento\Sales\Model\Order
     * @throws \Exception
     */
    protected function _getOrder()
    {
        if (!$this->_order) {
            $orderId = $this->getRequest()->getPost('order_id');
            if (!$orderId) {
                throw new \Exception('Order ID is empty');
            }
            $this->_adyenLogger->addAdyenResult('order id is #' . $orderId);
            $this->_orderFactory = $this->_objectManager->get('Magento\Sales\Model\OrderFactory');
            $this->_order = $this->_orderFactory->create()->load($orderId);
        }
        return $this->_order;
    }

    public function restoreQuote($order)
    {
        if ($order->getId()) {
            try {
                $quote = $this->quoteFactory->create();
                $quote->setStoreId($order->getStoreId())->load($order->getQuoteId());

                if ($quote->getId()) {
                    $quote->setIsActive(1)->setReservedOrderId(null);
                    $quote->save();

                    return true;
                }

            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            }
        }

        return false;
    }
}
