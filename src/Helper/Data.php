<?php

namespace Hatimeria\Reagento\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    public function getAppLogoImg()
    {
        return $this->getConfigValue('app_logo_img');
    }

    public function getAppHomeUrl()
    {
        return $this->getConfigValue('app_home_url');
    }

    private function getConfigValue($key, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue("hatimeria/reagento/$key", $scope);
    }
}