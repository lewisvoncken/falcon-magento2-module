<?php

namespace Deity\MagentoApi\Controller\Quote;

use Magento\Checkout\Helper\Cart as CartHelper;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

/**
 * Switching
 * @package Deity\MagentoApi\Controller\Quote
 */
class Index extends Action
{
    /** @var Session */
    private $session;
    
    /** @var CartHelper */
    private $cartHelper;

    /**
     * @param Context $context
     * @param Session $session
     * @param CartHelper $cartHelper
     */
    public function __construct(Context $context, Session $session, CartHelper $cartHelper) {
        parent::__construct($context);
        $this->session = $session;
        $this->cartHelper = $cartHelper;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        if(($quoteID = (int)$this->getRequest()->getParam('id'))) {
            $this->session->setQuoteId($quoteID);
        }
        return null;
    }
}