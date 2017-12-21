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
     * @param \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceItemInterface[] $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceInterface
     */
    public function setItems($param);

    /**
     * @return string | null
     */
    public function getNumberOfLines();

    /**
     * @param $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceInterface
     */
    public function setNumberOfLines($param);

    /**
     * @return string | null
     */
    public function getRefundDescription();

    /**
     * @param $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectOpenInvoiceInterface
     */
    public function setRefundDescription($param);
}