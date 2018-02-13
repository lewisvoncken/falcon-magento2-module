<?php

namespace Deity\MagentoApi\Api;

interface QuoteMaskInterface
{
    /**
     * @param string $quoteId
     * @return \Deity\MagentoApi\Api\Data\OrderInfoInterface
     */
    public function getItem($quoteId);
}