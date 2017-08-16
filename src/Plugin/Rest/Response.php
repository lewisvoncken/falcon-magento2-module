<?php

namespace Hatimeria\Reagento\Plugin\Rest;

use Hatimeria\Reagento\Helper\Data as ReagentoHelper;

class Response
{
    /**
     * @var ReagentoHelper
     */
    protected $reagentoHelper;
    /**
     * @param ReagentoHelper $reagentoHelper
     */
    public function __construct(
        ReagentoHelper $reagentoHelper
    ) {
        $this->reagentoHelper = $reagentoHelper;
    }

    /**
     * Adds cache tags header if registry has values
     *
     * @param \Magento\Framework\Webapi\Rest\Response $subject
     * @param \Magento\Framework\Webapi\Rest\Response $result
     *
     * @return \Magento\Framework\Webapi\Rest\Response
     */
    public function afterPrepareResponse(\Magento\Framework\Webapi\Rest\Response $subject, $result)
    {
        $tags = [ReagentoHelper::defaultResponseTag];
        $registeredTags = $this->reagentoHelper->getResponseTags();
        if (!empty($registeredTags)) {
            $tags = array_merge($tags, $registeredTags);
        }
        $subject->setHeader('X-Cache-Tags', implode(',', $tags));

        return $result;
    }

}
