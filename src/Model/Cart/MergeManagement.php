<?php
namespace Deity\MagentoApi\Model\Cart;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Psr\Log\LoggerInterface;

/**
 * Handle merging guest and customer quote when signing up and signing in
 *
 * @package Deity\MagentoApi\Model\Cart
 */
class MergeManagement
{
    /** @var QuoteIdMaskFactory */
    private $quoteIdMaskFactory;

    /** @var CartRepositoryInterface */
    private $cartRepository;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var LoggerInterface */
    private $logger;

    /**
     * MergeManagement constructor.
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param CartRepositoryInterface $cartRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        CartRepositoryInterface $cartRepository,
        CustomerRepositoryInterface $customerRepository,
        LoggerInterface $logger
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->cartRepository = $cartRepository;
        $this->logger = $logger;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Merge guest quote to customer or convert guest quote if customer does not have active one
     *
     * @param string $guestQuoteId
     * @param string $username
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function mergeGuestAndCustomerQuotes($guestQuoteId, $username)
    {
        $customerCart = $this->getCustomerCart($username);
        if ($customerCart) {
            $guestCart = $this->getGuestCart($guestQuoteId);
            $customerCart->merge($guestCart);
            $this->cartRepository->save($customerCart);
        } else {
            //no active quote for logged in customer, convert guest to registered
            $customer = $this->customerRepository->get($username);
            $this->convertGuestCart($guestQuoteId, $customer);
        }

        return true;
    }

    /**
     * Convert guest quote to customer quote
     *
     * @param $guestQuoteId
     * @param CustomerInterface $customer
     * @return bool
     * @throws NoSuchEntityException
     */
    public function convertGuestCart($guestQuoteId, CustomerInterface $customer)
    {
        $guestQuote = $this->getGuestCart($guestQuoteId);
        $guestQuote->assignCustomer($customer);
        $guestQuote->setCheckoutMethod(null);
        $this->cartRepository->save($guestQuote);

        return true;
    }

    /**
     * Get provided guest quote
     *
     * @param $guestQuoteId
     * @return Quote|CartInterface
     * @throws NoSuchEntityException
     */
    private function getGuestCart($guestQuoteId)
    {
        /** @var QuoteIdMask $quoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create();
        $quoteIdMask->load($guestQuoteId, 'masked_id');
        $guestCart = $this->cartRepository->getActive($quoteIdMask->getQuoteId());

        return $guestCart;
    }

    /**
     * Get active logged in customer quote
     *
     * @param $username
     * @return Quote|CartInterface|null
     */
    private function getCustomerCart($username)
    {
        try {
            $customerCart = null;

            /** @var CustomerInterface $customer */
            $customer = $this->customerRepository->get($username);
            $customerCart = $this->cartRepository->getActiveForCustomer($customer->getId());
        } catch (NoSuchEntityException $e) {
            //all ok, registered customer did not have active quote
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }

        return $customerCart;
    }
}