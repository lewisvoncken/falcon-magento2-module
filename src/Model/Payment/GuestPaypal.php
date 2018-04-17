<?php
namespace Deity\MagentoApi\Model\Payment;

use Deity\MagentoApi\Api\Payment\Data\PaypalDataInterface;
use Deity\MagentoApi\Api\Payment\GuestPaypalInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Deity\MagentoApi\Model\Payment\PaypalDataFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Paypal\Model\Config;
use Magento\Paypal\Model\Express\Checkout\Factory as PaypalCheckoutFactory;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Checkout\Helper\Data;
use Magento\Checkout\Model\Type\Onepage;
use Magento\Paypal\Model\Express\Checkout;

/**
 * Class Payment
 * @package Deity\MagentoApi\Model\Payment
 */
class GuestPaypal implements GuestPaypalInterface
{
    /**
     * Config mode type
     *
     * @var string
     */
    private $configType = 'Magento\Paypal\Model\Config';

    /**
     * Config method type
     *
     * @var string
     */
    private $configMethod = Config::METHOD_WPP_EXPRESS;

    /**
     * Checkout mode type
     *
     * @var string
     */
    private $checkoutType = 'Magento\Paypal\Model\Express\Checkout';

    /**
     * @var Config
     */
    private $config;

    /**
     * Url
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * Payment Data Factory
     * @var PaymentDataFactory
     */
    private $factory;

    /**
     * Object Manager
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Quote Mask Factory
     * @var QuoteIdMaskFactory
     */
    private $quoteMaskFactory;

    /**
     * Cart Repository
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * Quote
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /**
     * Paypal Checkout
     * @var \Magento\Paypal\Model\Express\Checkout
     */
    protected $checkout;

    /**
     * Internal cache of checkout models
     *
     * @var array
     */
    protected $checkoutTypes = [];


    /**
     * @var Checkout\Factory
     */
    protected $checkoutFactory;

    /**
     * Payment constructor.
     * @param UrlInterface $urlBuilder
     * @param PaypalDataFactory $factory
     * @param ObjectManagerInterface $objectManager
     * @param QuoteIdMaskFactory $quoteMaskFactory
     * @param CartRepositoryInterface $cartRepository
     * @param Checkout\Factory $checkoutFactory
     */
    public function __construct(
        UrlInterface $urlBuilder,
        PaypalDataFactory $factory,
        ObjectManagerInterface $objectManager,
        QuoteIdMaskFactory $quoteMaskFactory,
        CartRepositoryInterface $cartRepository,
        PaypalCheckoutFactory $checkoutFactory
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->factory = $factory;
        $this->objectManager = $objectManager;
        $this->quoteMaskFactory = $quoteMaskFactory;
        $this->cartRepository = $cartRepository;
        $this->checkoutFactory = $checkoutFactory;
        $parameters = ['params' => [$this->configMethod]];
        $this->config = $this->objectManager->create($this->configType, $parameters);
    }

    /**
     * Quote
     * @return CartInterface|Quote
     */
    private function getQuote()
    {
        return $this->quote;
    }

    /**
     * @param CartInterface|Quote $quote
     * @return GuestPaypalInterface
     */
    public function setQuote(CartInterface $quote)
    {
        $this->quote = $quote;

        return $this;
    }

    /**
     * Instantiate quote and checkout
     *
     * @throws LocalizedException
     */
    private function initCheckout()
    {
        $quote = $this->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            throw new LocalizedException(__('We can\'t initialize Express Checkout.'));
        }
        if (!isset($this->checkoutTypes[$this->checkoutType])) {
            $parameters = [
                'params' => [
                    'quote' => $quote,
                    'config' => $this->config,
                ],
            ];
            $this->checkoutTypes[$this->checkoutType] = $this->checkoutFactory
                ->create($this->checkoutType, $parameters);
        }
        $this->checkout = $this->checkoutTypes[$this->checkoutType];
    }

    /**
     * @return string|null
     * @param $cartId string
     * @throws LocalizedException
     */
    protected function createToken($cartId)
    {
        $this->initCheckout();
        $quote = $this->getQuote();
        $hasButton = false; // @todo needs to be parametrized. Parameter: button=[1 / 0]

        /** @var Data $checkoutHelper */
        $checkoutHelper = $this->objectManager->get(Data::class);
        $quoteCheckoutMethod = $quote->getCheckoutMethod();

        if ($quote->getIsMultiShipping()) {
            $quote->setIsMultiShipping(false);
            $quote->removeAllAddresses();
        }

        if (
            (!$quoteCheckoutMethod || $quoteCheckoutMethod !== Onepage::METHOD_REGISTER)
            && !$checkoutHelper->isAllowedGuestCheckout($quote, $quote->getStoreId())
        ) {
            throw new LocalizedException(__('To check out, please sign in with your email address.'));
        }

        // billing agreement
        $this->checkout->setIsBillingAgreementRequested(false);

        // Bill Me Later
        $this->checkout->setIsBml(false); // @todo needs to be parametrized. Parameter: bml=[1 / 0]

        // giropay
        $this->checkout->prepareGiropayUrls(
            $this->urlBuilder->getUrl('checkout/onepage/success'),
            $this->urlBuilder->getUrl('paypal/express/cancel', ['cart_id' => $cartId]),
            $this->urlBuilder->getUrl('checkout/onepage/success')
        );

        return $this->checkout->start(
            $this->urlBuilder->getUrl('checkoutExt/payment_paypal_express/return', ['cart_id' => $cartId]),
            $this->urlBuilder->getUrl('checkoutExt/payment_paypal_express/cancel', ['cart_id' => $cartId]),
            $hasButton
        );
    }

    /**
     * Initialize Quote based on masked Id
     * @param $cartId
     * @return CartInterface|Quote
     * @throws NoSuchEntityException
     */
    protected function initQuote($cartId)
    {
        // Unmask quote:
        $quoteMask = $this->quoteMaskFactory->create()->load($cartId, 'masked_id');
        $this->setQuote($this->cartRepository->getActive($quoteMask->getQuoteId()));

        return $this->getQuote();
    }


    /**
     * Fetch PayPal token
     * @param string $cartId
     * @return PaypalDataInterface
     * @throws NoSuchEntityException
     */
    public function getToken($cartId)
    {
        $this->initQuote($cartId);
        $paymentData = $this->factory->create();

        try {
            $token = $this->createToken($cartId);
            $url = $this->checkout->getRedirectUrl();

            $paymentData->setToken($token);
            $paymentData->setUrl($url);
        } catch(LocalizedException $e) {
            $paymentData->setError($e->getMessage());
        }

        return $paymentData;
    }
}
