<?php

namespace Imega\FinanceGateway\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Imega\FinanceModule\Helper\CheckoutData;


class PaymentMethodAvailable implements ObserverInterface
{
    const FINANCE_FILTER_EXCLUDE = 'exclude';
    const PAYMENT_CODE = 'financegateway';
    const MULTI_PRODUCT_ITEMS = ['bundle'];

    protected $checkoutSession;

    public function __construct(CheckoutSession $checkoutSession, CheckoutData $checkoutData) {
        $this->checkoutSession = $checkoutSession;
        $this->checkoutData = $checkoutData;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if($observer->getEvent()->getMethodInstance()->getCode()==self::PAYMENT_CODE){
            if($this->orderIsExcluded()){
              $checkResult = $observer->getEvent()->getResult();
              $checkResult->setData('is_available', false);
            }
        }
    }

    protected function orderIsExcluded()
    {
        $items = $this->checkoutSession->getQuote()->getAllVisibleItems();
        $productKeywords =[];
        $productCount = 0;
        foreach ($items as $item) {
          if (in_array($item->getProductType(),self::MULTI_PRODUCT_ITEMS)){
            foreach($item->getChildren() as $childItem){
              $filter = $childItem->getProduct()->getImegamediaFinanceFilter();
              if(!empty($filter)){
                array_push($productKeywords, $filter);
              }
              $productCount++;
            }
          } else {
            $filter = $item->getProduct()->getImegamediaFinanceFilter();
            if(!empty($filter)){
              array_push($productKeywords, $filter);
            }
            $productCount++;
          }
        }
        if(in_array(self::FINANCE_FILTER_EXCLUDE, array_map('strtolower', $productKeywords))){
          return true;
        }

        if(
          $this->checkoutData->disablePaymentMethodIfNoFilter()
          && (!count($productKeywords) || count($productKeywords)!=$productCount)
        ){
          return true;
        }

        return false;
    }
}
