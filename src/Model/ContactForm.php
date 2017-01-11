<?php

namespace Hatimeria\Reagento\Model;

use Hatimeria\Reagento\Api\ContactFormInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;

class ContactForm implements ContactFormInterface
{
    /**
     * @var TransportBuilder
     */
    private $_transportBuilder;

    /** @var StoreManagerInterface */
    private $_storeManager;

    private $_request;

    /**
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param RequestInterface $request
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        RequestInterface $request
    ) {
        $this->_transportBuilder = $transportBuilder;
        $this->_storeManager = $storeManager;
        $this->_request = $request;
    }

    /**
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

        $transport = $this->_transportBuilder->setTemplateIdentifier('contact_email_email_template')
            ->setTemplateOptions([
                'area' => Area::AREA_FRONTEND,
                'store' => $this->_storeManager->getStore()->getId(),
            ])
            ->setTemplateVars(['data' => [
                'comment' => $message,
                'name' => $name,
                'email' => $email,
                'telephone' => $telephone,
            ]])
            ->setFrom(['name' => $name, 'email' => $email])
            ->addTo($sendTo)
            ->setReplyTo($email)
            ->getTransport();

        $transport->sendMessage();
    }
}