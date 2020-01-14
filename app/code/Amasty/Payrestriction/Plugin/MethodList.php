<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */

namespace Amasty\Payrestriction\Plugin;

class MethodList
{
    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentHelper;

    /**
     * @param \Magento\Payment\Helper\Data $paymentHelper
     *
     */
    public function __construct(
        \Magento\Payment\Helper\Data $paymentHelper
    )
    {
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * Fix for Amasty and M2 2.1.3 Update:
     * @param \Magento\Payment\Model\MethodList $subject
     * @param \Closure $proceed
     */

    public function aroundGetAvailableMethods(
        \Magento\Payment\Model\MethodList $subject,
        \Closure $proceed,
        \Magento\Quote\Api\Data\CartInterface $quote
    )
    {
        $paymentMethods = $proceed($quote);
        $store = $quote ? $quote->getStoreId() : null;
        $storePaymentMethods = $this->paymentHelper->getStoreMethods($store, $quote);

        $retArray = array();
        foreach ($storePaymentMethods as $method) {
            foreach ($paymentMethods as $methodFromList) {
                if ($method->getCode() == $methodFromList->getCode()) {
                    $retArray[] = $method;
                }
            }
        }
        return $retArray;

    }
}