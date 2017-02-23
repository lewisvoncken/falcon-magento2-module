<?php

namespace Hatimeria\Reagento\Api;

interface QuoteMaskInterface
{
    /**
     * @param string $quoteId
     * @return \Hatimeria\Reagento\Api\Data\OrderInfoInterface
     */
    public function getItem($quoteId);
}