<?php

namespace Hatimeria\Reagento\Model;

use Hatimeria\Reagento\Api\ContactFormInterface;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;

class ContactForm implements ContactFormInterface
{
    /** @var TransportBuilder */
    private $_transportBuilder;

    /** @var StoreManagerInterface */
    private $_storeManager;

    /** @var RequestInterface */
    private $_request;

    /** @var ScopeConfigInterface */
    private $_scopeConfig;

    /**
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param RequestInterface $request
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        RequestInterface $request,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->_transportBuilder = $transportBuilder;
        $this->_storeManager = $storeManager;
        $this->_request = $request;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * @throws \Exception
     * @return void
     */
    public function send()
    {
        $req = $this->_request;

        $message = $req->getParam('message');
        $firstName = $req->getParam('firstName');
        $lastName = $req->getParam('lastName');
        $name = "$firstName $lastName";
        $email = $req->getParam('email');
        $telephone = $req->getParam('telephone');
        $sendTo = $req->getParam('sendTo');

        $dataObject = new DataObject();
        $dataObject->setData([
            'comment' => $message,
            'name' => $name,
            'email' => $email,
            'telephone' => $telephone,
        ]);

        $transport = $this->_transportBuilder
            ->setTemplateIdentifier($this->_scopeConfig->getValue('contact/email/email_template', 'store'))
            ->setTemplateOptions([
                'area' => FrontNameResolver::AREA_CODE,
                'store' => $this->_storeManager->getStore()->getId(),
            ])
            ->setTemplateVars(['data' => $dataObject])
            ->setFrom(['name' => $name, 'email' => $email])
            ->addTo($sendTo)
            ->setReplyTo($email)
            ->getTransport();

        $transport->sendMessage();
    }
}