<?php

namespace Deity\MagentoApi\Api\Data;

interface InfoDataInterface
{
    /**
     * @return string
     */
    public function getCustomerRegisterUrl();

    /**
     * @param string $data
     * @return $this
     */
    public function setCustomerRegisterUrl($data);

    /**
     * @return string
     */
    public function getCustomerDashboardUrl();

    /**
     * @param string $data
     * @return $this
     */
    public function setCustomerDashboardUrl($data);
}