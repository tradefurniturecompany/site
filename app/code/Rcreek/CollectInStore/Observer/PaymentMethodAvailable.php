<?php

namespace Rcreek\CollectInStore\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class PaymentMethodAvailable implements ObserverInterface
{
    protected $_logger;

    public function __construct(
        LoggerInterface $logger
    )
    {
        $this->_logger = $logger;
    }

    /**
     * payment_method_is_active event handler.
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart');
        $quote = $cart->getQuote();
        $methodTitle = $quote->getShippingAddress()->getShippingMethod();

//        $this->_logger->info('methodTitle ' . $methodTitle);
//        $this->_logger->info('paymethodTitle ' . $observer->getEvent()->getMethodInstance()->getCode());

        if ($methodTitle === "amstrates_amstrates2") {
            if ($observer->getEvent()->getMethodInstance()->getCode() !== "cashondelivery") {
                $checkResult = $observer->getEvent()->getResult();
                $checkResult->setData('is_available', false);
            }
        } else {
            if ($observer->getEvent()->getMethodInstance()->getCode() === "cashondelivery") {
                $checkResult = $observer->getEvent()->getResult();
                $checkResult->setData('is_available', false);
            }
        }

    }
}
