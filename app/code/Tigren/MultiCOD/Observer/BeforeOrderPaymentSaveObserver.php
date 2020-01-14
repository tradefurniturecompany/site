<?php
/**
 * Copyright (c) 2017 www.tigren.com
 */

namespace Tigren\MultiCOD\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class BeforeOrderPaymentSaveObserver
 * @package Tigren\MultiCOD\Observer
 */
class BeforeOrderPaymentSaveObserver implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $observer->getEvent()->getPayment();
        $instructionMethods = [
            'cashondelivery1',
        ];
        if (in_array($payment->getMethod(), $instructionMethods)) {
            $payment->setAdditionalInformation(
                'instructions',
                $payment->getMethodInstance()->getInstructions()
            );
        }
    }
}
