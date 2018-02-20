<?php

namespace Deity\MagentoApi\Model;

use Deity\MagentoApi\Api\Data\InfoDataInterface;
use Magento\Framework\Model\AbstractModel;

class InfoData extends AbstractModel implements InfoDataInterface
{
    const KEY_REGISTER_URL = 'customer_register_url';
    const KEY_DASHBOARD_URL = 'customer_dashboard_url';

    public function getCustomerRegisterUrl()
    {
        return $this->getData(self::KEY_REGISTER_URL);
    }

    public function setCustomerRegisterUrl($data)
    {
        return $this->setData(self::KEY_REGISTER_URL, $data);
    }

    public function getCustomerDashboardUrl()
    {
        return $this->getData(self::KEY_DASHBOARD_URL);
    }

    public function setCustomerDashboardUrl($data)
    {
        return $this->setData(self::KEY_DASHBOARD_URL, $data);
    }
}