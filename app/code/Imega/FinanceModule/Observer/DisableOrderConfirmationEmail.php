<?php

namespace Imega\FinanceModule\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Imega\FinanceModule\Helper\Data;

class DisableOrderConfirmationEmail implements \Magento\Framework\Event\ObserverInterface
{
    protected $config;

    public function __construct(Data $config) {
        $this->config = $config;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if(!$this->config->getEnableOrderEmailOnCustomStatus()){
          return null;
        }
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $observer->getEvent()->getOrder();
        $this->_current_order = $order;

        $this->disableConfirmationEmail($order);
    }

    private function disableConfirmationEmail(\Magento\Sales\Model\Order $order)
    {
      $paymentCode = $order->getPayment()->getMethodInstance()->getCode();

      if ($paymentCode == 'financegateway') {
          $order->setCanSendNewEmailFlag(false);
          $order->setSendEmail(false);
          $order->save();
      }
    }
}
