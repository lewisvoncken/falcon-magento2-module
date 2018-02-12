<?php

namespace Deity\MagentoApi\Api\Data;

interface OrderInfoInterface
{
    /**
     * @return int
     */
    public function getOrderId();

    /**
     * @param int $id
     * @return \Deity\MagentoApi\Api\Data\OrderInfoInterface
     */
    public function setOrderId($id);

    /**
     * @return mixed
     */
    public function getRevenue();

    /**
     * @param mixed $revenue
     * @return \Deity\MagentoApi\Api\Data\OrderInfoInterface
     */
    public function setRevenue($revenue);

    /**
     * @return mixed
     */
    public function getShipping();

    /**
     * @param mixed $shipping
     * @return \Deity\MagentoApi\Api\Data\OrderInfoInterface
     */
    public function setShipping($shipping);

    /**
     * @return mixed
     */
    public function getTax();

    /**
     * @param mixed $tax
     * @return \Deity\MagentoApi\Api\Data\OrderInfoInterface
     */
    public function setTax($tax);

    /**
     * @return mixed
     */
    public function getQuoteId();

    /**
     * @param mixed $id
     * @return \Deity\MagentoApi\Api\Data\OrderInfoInterface
     */
    public function setQuoteId($id);

    /**
     * @return mixed
     */
    public function getMaskedId();

    /**
     * @param string $id
     * @return \Deity\MagentoApi\Api\Data\OrderInfoInterface
     */
    public function setMaskedId($id);
}