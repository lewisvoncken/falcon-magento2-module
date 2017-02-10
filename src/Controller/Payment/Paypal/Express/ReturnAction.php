<?php

namespace Hatimeria\Reagento\Controller\Payment\Paypal\Express;

use Exception;
use Magento\Quote\Model\Quote;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Model\Config\Source\Price\Scope;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\OrderFactory;
use Magento\Paypal\Model\Express\Checkout\Factory;
use Magento\Framework\Session\Generic;
use Magento\Framework\Url\Helper\Data as HelperData;
use Magento\Customer\Model\Url;
use Magento\Framework\Controller\ResultFactory;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Url as UrlBuilder;

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
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Url
     */
    protected $urlBuilder;

    /**
     * ReturnAction constructor.
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param CheckoutSession $checkoutSession
     * @param OrderFactory $orderFactory
     * @param Factory $checkoutFactory
     * @param Generic $paypalSession
     * @param HelperData $urlHelper
     * @param Url $customerUrl
     * @param QuoteIdMaskFactory $quoteMaskFactory
     * @param CartRepositoryInterface $cartRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param LoggerInterface $logger
     * @param \Magento\Framework\Url $urlBuilder
     */
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
        CartRepositoryInterface $cartRepository,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger,
        UrlBuilder $urlBuilder
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
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Quote
     * @return Quote
     */
    protected function _getQuote()
    {
        return $this->_quote;
    }

    /**
     * @param Quote $quote
     * @return GetToken
     */
    public function setQuote(Quote $quote)
    {
        $this->_quote = $quote;

        return $this;
    }


    /**
     * Initialize Quote based on masked Id
     * @param $cartId
     * @return Quote
     */
    protected function initQuote($cartId)
    {
        // Unmask quote:
        $quoteMask = $this->quoteMaskFactory->create()->load($cartId, 'masked_id');
        $quote = $this->cartRepository->getActive($quoteMask->getQuoteId());
        $this->setQuote($quote);

        return $this->_quote;
    }

    /**
     * Execute Action
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $request = $this->getRequest();
        $cartId = $request->getParam('cart_id');
        $token = $request->getParam('token');
        $payerId = $request->getParam('PayerID');
        $redirectUrlFailure = $this->scopeConfig->getValue(
            'hatimeria/payment/paypal_redirect_failure',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
        $redirectUrl = false;
        $message = __('');

        try {
            $this->initQuote($cartId);
            $this->_initCheckout();
            $this->_checkout->returnFromPaypal($token);

            if ($this->_checkout->canSkipOrderReviewStep()) {
                $this->placeOrder($token, $cartId, $payerId);

                // redirect if PayPal specified some URL (for example, to Giropay bank)
                $url = $this->_checkout->getRedirectUrl();
                if ($url) {
                    $redirectUrl = $url;
                } else {
                    $redirectUrl = $this->scopeConfig->getValue(
                        'hatimeria/payment/paypal_redirect_success',
                        ScopeConfigInterface::SCOPE_TYPE_DEFAULT
                    );
                    $message = __('Your Order got a number: #%1', $this->_checkout->getOrder()->getIncrementId());
                }
            } else {
                throw new LocalizedException(__('Review page is not supported!'));
            }

        } catch (LocalizedException $e) {
            $this->logger->critical('PayPal Return Action: ' . $e->getMessage());
            $redirectUrl = $redirectUrlFailure;
            $message = __('Reason: %1', $e->getMessage());
        } catch (Exception $e) {
            $this->logger->critical('PayPal Return Action: ' . $e->getMessage());
            $message = __('Reason: %1', $e->getMessage());
            $redirectUrl = $redirectUrlFailure;
        }

        if (strpos($redirectUrl, 'http') !== false) {
            $sep = (strpos($redirectUrl, '?') === false) ? '?' : '&';
            return $resultRedirect->setUrl(sprintf('%s%s%s=%s',
                $redirectUrl,
                $sep,
                ActionInterface::PARAM_NAME_URL_ENCODED,
                base64_encode((string)$message)
            ));
        } else {
            return $resultRedirect->setUrl($this->urlBuilder->getBaseUrl()
                . sprintf('%s?%s=%s',
                    trim($redirectUrl, ' /'),
                    ActionInterface::PARAM_NAME_URL_ENCODED,
                    base64_encode((string)$message)
                ));
        }
    }

    /**
     * Place Order
     * @param $token
     * @param $cartId
     * @param $payerId
     */
    protected function placeOrder($token, $cartId, $payerId)
    {
        // Fix: reinitialize quote and checkout.
        $this->resetCheckoutTypes();
        $this->initQuote($cartId);
        $this->_initCheckout();

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
    }

    /**
     * Reset checkout types
     */
    protected function resetCheckoutTypes()
    {
        unset($this->_checkoutTypes);
        $this->_checkoutTypes = [];
    }
}