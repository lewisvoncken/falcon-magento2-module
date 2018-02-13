<?php
namespace Deity\MagentoApi\Controller\Payment\Paypal\Express;

use Psr\Log\LoggerInterface;
use Magento\Paypal\Controller\Express\Cancel as CancelAction;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Cancel
 * @package Deity\MagentoApi\Controller
 */
class Cancel extends CancelAction
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
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /**
     * @var LoggerInterface
     */
    protected $logger;

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
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Paypal\Model\Express\Checkout\Factory $checkoutFactory,
        \Magento\Framework\Session\Generic $paypalSession,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Customer\Model\Url $customerUrl,
        QuoteIdMaskFactory $quoteMaskFactory,
        CartRepositoryInterface $cartRepository,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger
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
     * @return Cancel
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

        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->critical('PayPal Cancel Action: ' . $e->getMessage());
            $redirectUrl = $redirectUrlFailure;
        } catch (\Exception $e) {
            $this->logger->critical('PayPal Cancel Action: ' . $e->getMessage());
            $redirectUrl = $redirectUrlFailure;
        }

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if (strpos($redirectUrl, 'http') !== false) {
            return $resultRedirect->setUrl($redirectUrl);
        } else {
            return $resultRedirect->setPath($redirectUrl);
        }
    }
}
