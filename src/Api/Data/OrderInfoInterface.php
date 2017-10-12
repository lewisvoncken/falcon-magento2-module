<?php

namespace Hatimeria\Reagento\Api\Data;

interface OrderInfoInterface
{
    /**
     * @return int
     */
    public function getOrderId();

    /**
     * @param int $id
     * @return \Hatimeria\Reagento\Api\Data\OrderInfoInterface
     */
    public function setOrderId($id);

    /**
     * @return mixed
     */
    public function getRevenue();

    /**
     * @param mixed $revenue
     * @return \Hatimeria\Reagento\Api\Data\OrderInfoInterface
     */
    public function setRevenue($revenue);

    /**
     * @return mixed
     */
    public function getShipping();

    /**
     * @param mixed $shipping
     * @return \Hatimeria\Reagento\Api\Data\OrderInfoInterface
     */
    public function setShipping($shipping);

    /**
     * @return mixed
     */
    public function getTax();

    /**
     * @param mixed $tax
     * @return \Hatimeria\Reagento\Api\Data\OrderInfoInterface
     */
    public function setTax($tax);

    /**
     * @return mixed
     */
    public function getQuoteId();

    /**
     * @param mixed $id
     * @return \Hatimeria\Reagento\Api\Data\OrderInfoInterface
     */
    public function setQuoteId($id);

    /**
     * @return mixed
     */
    public function getMaskedId();

    /**
     * @param string $id
     * @return \Hatimeria\Reagento\Api\Data\OrderInfoInterface
     */
    public function setMaskedId($id);
}