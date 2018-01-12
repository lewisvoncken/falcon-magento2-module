<?php
namespace Hatimeria\Reagento\Plugin\Adyen\Controller\Process;

use Adyen\Payment\Controller\Process\Result;
use Hatimeria\Reagento\Observer\Adyen\ProcessResultRestoreQuote;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Zend\Http\Header\Location;

class ResultPlugin
{
    /** @var OrderRepositoryInterface */
    protected $orderRepository;

    /** @var SearchCriteriaBuilder */
    protected $searchCriteriaBuilder;

    /** @var UrlInterface */
    protected $urlBuilder;

    /** @var Registry */
    protected $registry;

    /** @var ManagerInterface */
    protected $messageManager;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        UrlInterface $urlBuilder,
        Registry $registry,
        ManagerInterface $messageManager
    ) {
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->urlBuilder = $urlBuilder;
        $this->registry = $registry;
        $this->messageManager = $messageManager;
    }

    public function afterDispatch(Result $controller, Http $result)
    {
        if (!$result->isRedirect()) {
            return;
        }

        /** @var Location $location */
        $location = $result->getHeader('Location');
        $redirectPath = $location->uri()->getPath();
        $url = null;
        if ($redirectPath === '/checkout/onepage/success/') {
            $params = $controller->getRequest()->getParams();
            $order = $this->getOrder($params['merchantReference']);
            $message = (string)__('Your order number is #%1', $order->getIncrementId());
            $url = $this->urlBuilder->getUrl(
                'checkout/result/success',
                ['_query' => [
                    'adyen_redirect' => 1,
                    'adyen_success' => true,
                    'uenc' => base64_encode($message),
                    'order_id' => $order->getIncrementId()
                ]]
            );
        } elseif ($quoteId = $this->registry->registry(ProcessResultRestoreQuote::REGISTRY_KEY)) {
            /** @var MessageInterface $message */
            $message = $this->messageManager->getMessages()->getLastAddedMessage();
            $url = $this->urlBuilder->getUrl(
                'checkout/result/failure',
                ['_query' => [
                    'quote_id' => $quoteId,
                    'adyen_redirect' => 1,
                    'uenc' => base64_encode($message->getText())
                ]]
            );
        }
        if ($url) {
            $url = str_replace('rebul.test', 'localhost:3000', $url);
            $result->setRedirect($url);
        }

        return $result;
    }


    /**
     * Get order by increment id
     *
     * @param string $incrementId
     * @return OrderInterface|null
     */
    protected function getOrder($incrementId)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('increment_id', $incrementId, 'eq');
        $orders = $this->orderRepository->getList($searchCriteria->create())->getItems();
        if (!empty($orders)) {
            /** @var OrderInterface $order */
            $order = array_shift($orders);
            return $order;
        }

        return null;
    }
}