<?php

namespace Imega\FinanceGateway\Controller\Index;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Model\Order;
use Imega\FinanceModule\Helper\Data;

class Index extends Action
{
    const CHECKOUT_URL = 'https://angus.finance-calculator.co.uk/api/public/finance-checkout';

    private $checkoutSession;

    protected $financeModuleData;



    public function __construct(
        Context $context,
        Session $checkoutSession,
        Data $financeModuleData
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->financeModuleData = $financeModuleData;
        parent::__construct($context);
    }


    public function execute()
    {
        $params = $this->getRequest()->getParams();

        $order = $this->checkoutSession->getLastRealOrder();
        $orderId = $order->getIncrementId();
        $total = $order->getGrandTotal();
        $customerData = $order->getBillingAddress();

        $items = $order->getAllVisibleItems();

        $apiKey = $this->financeModuleData->getApiKey();
        $encKey = $this->financeModuleData->getEncryptionKey();

        $veriString = $total.$orderId;
        $vKey = hash_hmac('sha256',$veriString, $encKey);

        $orderDesc = '';
        $productsArr = [];
        $financeKeywords = [];
        $i = 0;
        foreach($items as $item){
          $orderDesc.= htmlspecialchars($item->getName(), ENT_QUOTES).'|||';
          $productsArr[$i]['productId'] = $item->getSku();
          $productsArr[$i]['description'] = htmlspecialchars($item->getName(), ENT_QUOTES);
          $productsArr[$i]['price'] = $item->getPrice();
          $productsArr[$i]['quantity'] = $item->getQtyToInvoice();
          array_push($financeKeywords, $item->getProduct()->getImegamediaFinanceFilter());
          $i++;
        }
        $products = json_encode($productsArr);

        $query = [];
        $query['api_key'] = $apiKey;
        $query['v_key'] = $vKey;

        $query['loan_amount'] = $total;

        $query['finance_code'] =$params['finance_code'];
        $query['deposit'] = $params['deposit'];

        $query['order_ref'] = $orderId;
        $query['order_description'] = $orderDesc;

        $query['email'] = $customerData->getData("email");
        $query['first_name'] = $customerData->getData("firstname");
        $query['last_name'] = $customerData->getData("lastname");
        $address = $customerData->getData("street");
        $query['street'] = preg_replace( "/\r|\n/", ", ", $address);
        $query['house_number'] = substr($address, 0, strpos($address, ' '));
        $query['town'] = $customerData->getData("city");
        $query['region'] = $customerData->getData("region");
        $query['postcode'] = $customerData->getData("postcode");
        $query['telephone'] = $customerData->getData("telephone");

        $queryString = (http_build_query($query));

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl(self::CHECKOUT_URL.'?'.$queryString);

        return $resultRedirect;


    }
}
