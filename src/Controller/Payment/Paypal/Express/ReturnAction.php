<?php

namespace Hatimeria\Reagento\Controller\Payment\Paypal\Express;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Sales\Model\OrderFactory;
use Magento\Paypal\Model\Express\Checkout\Factory;
use Magento\Framework\Session\Generic;
use Magento\Framework\Url\Helper\Data as HelperData;
use Magento\Customer\Model\Url;
use Magento\Framework\Controller\ResultFactory;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * Class ReturnAction
 * @package Hatimeria\Reagento\Controller\Paypal\Express
 */
class ReturnAction extends \Magento\Paypal\Controller\Express\ReturnAction
{
    /**
     * @var QuoteIdMaskFactory
     */
    protected $quoteMaskFactory;

    /**
     * Quote
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CheckoutSession $checkoutSession,
        OrderFactory $orderFactory,
        Factory $checkoutFactory,
        Generic $paypalSession,
        HelperData $urlHelper,
        Url $customerUrl,
        QuoteIdMaskFactory $quoteMaskFactory,
        CartRepositoryInterface $cartRepository
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $checkoutSession,
            $orderFactory,
            $checkoutFactory,
            $paypalSession,
            $urlHelper,
            $customerUrl
        );
        $this->quoteMaskFactory = $quoteMaskFactory;
        $this->cartRepository = $cartRepository;
    }

    /**
     * Quote
     * @return \Magento\Quote\Model\Quote
     */
    protected function _getQuote()
    {
        return $this->quote;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return GetToken
     */
    public function setQuote(\Magento\Quote\Model\Quote $quote)
    {
        $this->quote = $quote;

        return $this;
    }


    /**
     * Initialize Quote based on masked Id
     * @param $cartId
     * @return \Magento\Quote\Model\Quote
     */
    protected function initQuote($cartId)
    {
        // Unmask quote:
        $quoteMask = $this->quoteMaskFactory->create()->load($cartId, 'masked_id');
        $this->setQuote($this->cartRepository->getActive($quoteMask->getQuoteId()));

        return $this->_getQuote();
    }

    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $request = $this->getRequest();
        $cartId = $request->getParam('cart_id');
        $token = $request->getParam('token');
        $payerId = $request->getParam('PayerID');

        try {

            $this->initQuote($cartId);
            $this->_initCheckout();
            $this->_checkout->returnFromPaypal($token);

            if ($this->_checkout->canSkipOrderReviewStep()) {
                $this->placeOrder($token, $payerId);

                die('Order is placed. Checkout can now redirect to React.');
            } else {
                $this->_redirect('*/*/review'); // @todo check it!
            }

        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                $e->getMessage()
            );
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('We can\'t process Express Checkout approval.')
            );
        }

        return $resultRedirect->setPath('checkout/cart');
    }

    /**
     * Place Order
     * @param $token
     * @param $payerId
     */
    protected function placeOrder($token, $payerId)
    {
        $this->_checkout->place($token);

        // prepare session to success or cancellation page
        $this->_getCheckoutSession()->clearHelperData();

        // "last successful quote"
        $quoteId = $this->_getQuote()->getId();
        $this->_getCheckoutSession()->setLastQuoteId($quoteId)->setLastSuccessQuoteId($quoteId);

        // an order may be created
        $order = $this->_checkout->getOrder();
        if ($order) {
            $this->_getCheckoutSession()->setLastOrderId($order->getId())
                ->setLastRealOrderId($order->getIncrementId())
                ->setLastOrderStatus($order->getStatus());
        }

        $this->_eventManager->dispatch(
            'paypal_express_place_order_success', [
                'order' => $order,
                'quote' => $this->_getQuote()
            ]
        );

        // redirect if PayPal specified some URL (for example, to Giropay bank)
        $url = $this->_checkout->getRedirectUrl();
        if ($url) {
            $this->getResponse()->setRedirect($url);
            return;
        }
        $this->_redirect('checkout/onepage/success');
    }
}