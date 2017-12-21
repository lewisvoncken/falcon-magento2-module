<?php

namespace Hatimeria\Reagento\Api\Data;

interface AdyenRedirectOpenInvoiceInterface
{
    const ITEMS = 'items';
    const NUMBER_OF_LINES = 'number_of_lines';
    const REFUND_DESCRIPTION = 'refund_description';

    /**
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface[]
     */
    public function getItems();

    /**
     * @param \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface[] $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceInterface
     */
    public function setItems($value);

    /**
     * @return integer | null
     */
    public function getNumberOfLines();

    /**
     * @param integer $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceInterface
     */
    public function setNumberOfLines($value);

    /**
     * @return string | null
     */
    public function getRefundDescription();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceInterface
     */
    public function setRefundDescription($value);
}