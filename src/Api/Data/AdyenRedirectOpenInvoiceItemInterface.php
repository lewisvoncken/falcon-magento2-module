<?php
namespace Hatimeria\Reagento\Api\Data;

interface AdyenRedirectOpenInvoiceItemInterface
{
    const CURRENCY_CODE = 'currency_code';
    const DESCRIPTION = 'description';
    const ITEM_AMOUNT = 'item_amount';
    const ITEM_VAT_AMOUNT = 'item_vat_amount';
    const ITEM_VAT_PERCENTAGE = 'item_vat_percentage';
    const NUMBER_OF_ITEMS = 'number_of_items';
    const VAT_CATEGORY = 'vat_category';

    /**
     * @return string | null
     */
    public function getCurrencyCode();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface
     */
    public function setCurrencyCode($value);

    /**
     * @return string | null
     */
    public function getDescription();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface
     */
    public function setDescription($value);

    /**
     * @return integer | null
     */
    public function getItemAmount();

    /**
     * @param integer $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface
     */
    public function setItemAmount($value);

    /**
     * @return integer | null
     */
    public function getItemVatAmount();

    /**
     * @param integer $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface
     */
    public function setItemVatAmount($value);

    /**
     * @return string | null
     */
    public function getItemVatPercentage();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface
     */
    public function setItemVatPercentage($value);

    /**
     * @return integer | null
     */
    public function getNumberOfItems();

    /**
     * @param integer $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface
     */
    public function setNumberOfItems($value);

    /**
     * @return string | null
     */
    public function getVatCategory();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface
     */
    public function setVatCategory($value);
}
