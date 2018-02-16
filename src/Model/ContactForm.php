<?php

namespace Deity\MagentoApi\Model;

use Deity\MagentoApi\Api\ContactFormInterface;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\DataObject;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;

class ContactForm implements ContactFormInterface
{
    /** @var TransportBuilder */
    private $_transportBuilder;

    /** @var StoreManagerInterface */
    private $_storeManager;

    /** @var RequestInterface|Http */
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

        $inputValue = file_get_contents('php://input');
        if($inputValue) {
            $inputRequest = json_decode($inputValue, true);
            foreach ($inputRequest as $key => $value) {
                $req->setPostValue($key, $value);
            }
        }

        $message = $req->getPostValue('message');
        $firstName = $req->getPostValue('firstName');
        $lastName = $req->getPostValue('lastName');
        $name = "$firstName $lastName";
        $email = $req->getPostValue('email');
        $telephone = $req->getPostValue('telephone');
        $sendTo = $req->getPostValue('sendTo');

        $senderEmail = $this->_scopeConfig->getValue('trans_email/ident_support/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $senderName  = $this->_scopeConfig->getValue('trans_email/ident_support/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $dataObject = new DataObject();
        $dataObject->setData([
            'comment' => $message,
            'name' => $name,
            'email' => $email,
            'telephone' => $telephone,
        ]);

        $storeName = $this->_scopeConfig->getValue(
            'general/store_information/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $transport = $this->_transportBuilder
            ->setTemplateIdentifier($this->_scopeConfig->getValue('contact/email/email_template', \Magento\Store\Model\ScopeInterface::SCOPE_STORE))
            ->setTemplateOptions([
                'area' => FrontNameResolver::AREA_CODE,
                'store' => $this->_storeManager->getStore()->getId(),
            ])
            ->setTemplateVars(['data' => $dataObject])
            ->setFrom(['name' => $senderName, 'email' => $senderEmail])
            ->addTo([$storeName => $sendTo])
            ->setReplyTo($email)
            ->getTransport();

        $transport->sendMessage();
    }
}