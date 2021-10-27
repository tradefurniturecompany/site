<?php

namespace Imega\FinanceModule\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\ObjectManagerInterface;
use Imega\FinanceModule\Helper\Data;

class SendOrderConfirmationEmail implements ObserverInterface
{
    protected $_objectManager;
    protected $orderRepository;
    protected $sender;
    protected $config;

    public function __construct(

      ObjectManagerInterface $objectManager,
      OrderRepositoryInterface $orderRepository,
      OrderSender $sender,
      Data $config

    ) {
        $this->_objectManager = $objectManager;
        $this->orderRepository = $orderRepository;
        $this->sender = $sender;
        $this->config = $config;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if(!$this->config->getEnableOrderEmailOnCustomStatus()){
          return null;
        }
        $order = $observer->getEvent()->getOrder();
        $newStatus = $order->getData('status');
        $paymentCode = $order->getPayment()->getMethodInstance()->getCode();

        if ($paymentCode == 'financegateway' && $newStatus == $this->config->getOrderEmailStatus()) {

            $order = $this->orderRepository->get($order->getId());

            if($order->getEmailSent()){
              return null;
            }

            $order->setCanSendNewEmailFlag(true);
            $this->sender->send($order, true);
        }
    }

}
