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
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface
     */
    public function setCurrencyCode($param);

    /**
     * @return string | null
     */
    public function getDescription();

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface
     */
    public function setDescription($param);

    /**
     * @return string | null
     */
    public function getItemAmount();

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface
     */
    public function setItemAmount($param);

    /**
     * @return string | null
     */
    public function getItemVatAmount();

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface
     */
    public function setItemVatAmount($param);

    /**
     * @return string | null
     */
    public function getItemVatPercentage();

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface
     */
    public function setItemVatPercentage($param);

    /**
     * @return string | null
     */
    public function getNumberOfItems();

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface
     */
    public function setNumberOfItems($param);

    /**
     * @return string | null
     */
    public function getVatCategory();

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface
     */
    public function setVatCategory($param);
}
