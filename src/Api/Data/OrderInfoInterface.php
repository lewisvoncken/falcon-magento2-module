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
     * @return mixed
     */
    public function setOrderId($id);

    /**
     * @return mixed
     */
    public function getRevenue();

    /**
     * @param mixed $revenue
     * @return mixed
     */
    public function setRevenue($revenue);

    /**
     * @return mixed
     */
    public function getShipping();

    /**
     * @param mixed $shipping
     * @return mixed
     */
    public function setShipping($shipping);

    /**
     * @return mixed
     */
    public function getTax();

    /**
     * @param mixed $tax
     * @return mixed
     */
    public function setTax($tax);
}