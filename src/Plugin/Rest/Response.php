<?php

namespace Deity\MagentoApi\Plugin\Rest;

use Deity\MagentoApi\Helper\Data as DeityHelper;

class Response
{
    /**
     * @var DeityHelper
     */
    protected $deityHelper;
    /**
     * @param DeityHelper $deityHelper
     */
    public function __construct(
        DeityHelper $deityHelper
    ) {
        $this->deityHelper = $deityHelper;
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
        $tags = [DeityHelper::defaultResponseTag];
        $registeredTags = $this->deityHelper->getResponseTags();
        if (!empty($registeredTags)) {
            $tags = array_merge($tags, $registeredTags);
        }
        $subject->setHeader('X-Cache-Tags', implode(',', $tags));

        return $result;
    }

}
