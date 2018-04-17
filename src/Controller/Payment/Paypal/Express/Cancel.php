<?php
namespace Deity\MagentoApi\Controller\Payment\Paypal\Express;

use Deity\MagentoApi\Helper\Data;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Model\Url;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Session\Generic;
use Magento\Framework\Url\Helper\Data as UrlHelper;
use Magento\Framework\UrlInterface;
use Magento\Paypal\Controller\Express\Cancel as CancelAction;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Paypal\Model\Express\Checkout\Factory;
use Psr\Log\LoggerInterface;

/**
 * Class Cancel
 * @package Deity\MagentoApi\Controller
 */
class Cancel extends CancelAction
{
    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteMaskFactory;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    private $quote;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Data
     */
    private $deityHelper;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * Cancel constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Paypal\Model\Express\Checkout\Factory $checkoutFactory
     * @param \Magento\Framework\Session\Generic $paypalSession
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param QuoteIdMaskFactory $quoteMaskFactory
     * @param CartRepositoryInterface $cartRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param LoggerInterface $logger
     * @param Data $deityHelper
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CheckoutSession $checkoutSession,
        OrderFactory $orderFactory,
        Factory $checkoutFactory,
        Generic $paypalSession,
        UrlHelper $urlHelper,
        Url $customerUrl,
        QuoteIdMaskFactory $quoteMaskFactory,
        CartRepositoryInterface $cartRepository,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger,
        Data $deityHelper,
        UrlInterface $urlBuilder
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
        $this->deityHelper = $deityHelper;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Quote
     * @return CartInterface|Quote
     */
    protected function _getQuote()
    {
        return $this->quote;
    }

    /**
     * @param CartInterface|Quote $quote
     * @return Cancel
     */
    public function setQuote(CartInterface $quote)
    {
        $this->quote = $quote;

        return $this;
    }


    /**
     * Initialize Quote based on masked Id
     * @param $cartId
     * @return CartInterface|Quote
     * @throws NoSuchEntityException
     */
    protected function initQuote($cartId)
    {
        if (!ctype_digit($cartId)) {
            $quoteMask = $this->quoteMaskFactory->create()->load($cartId, 'masked_id');
            $quoteId = $quoteMask->getQuoteId();
        } else {
            $quoteId = $cartId;
        }

        $this->setQuote($this->cartRepository->getActive($quoteId));

        return $this->_getQuote();
    }

    /**
     * Cancel Express Checkout
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $request = $this->getRequest();
        $cartId = $request->getParam('cart_id');
        $token = $request->getParam('token');
        $payerId = $request->getParam('PayerID');
        $redirectUrlFailure = $this->scopeConfig->getValue(
            'deity_payment/paypal/redirect_failure',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );

        try {
            $this->initQuote($cartId);

            $redirectUrl = $this->scopeConfig->getValue(
                'deity_payment/paypal/redirect_cancel',
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT
            );

        } catch (LocalizedException $e) {
            $this->logger->critical('PayPal Cancel Action: ' . $e->getMessage());
            $redirectUrl = $redirectUrlFailure;
        } catch (\Exception $e) {
            $this->logger->critical('PayPal Cancel Action: ' . $e->getMessage());
            $redirectUrl = $redirectUrlFailure;
        }

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $redirectUrl = $this->deityHelper->prepareFrontendUrl($this->urlBuilder->getUrl($redirectUrl));

        if (strpos($redirectUrl, 'http') !== false) {
            return $resultRedirect->setUrl($redirectUrl);
        } else {
            return $resultRedirect->setUrl($redirectUrl);
        }
    }
}
