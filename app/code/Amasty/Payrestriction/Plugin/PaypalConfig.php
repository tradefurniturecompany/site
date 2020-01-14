<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */


namespace Amasty\Payrestriction\Plugin;

use Magento\Paypal\Model\Config;

class PaypalConfig
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Amasty\Payrestriction\Model\Restrict
     */
    private $restrict;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    private $paymentHelper;

    /**
     * @var string
     */
    private $methodCode;

    /**
     * @var array
     */
    private $paypalExpressMethods = [
        Config::METHOD_WPS_EXPRESS,
        Config::METHOD_WPP_EXPRESS,
        Config::METHOD_WPP_PE_EXPRESS,
        Config::METHOD_EXPRESS,
    ];

    /**
     * @var \Magento\Framework\App\ProductMetadata
     */
    private $productMetadata;

    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Amasty\Payrestriction\Model\Restrict $restrict,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Framework\App\ProductMetadata $productMetadata
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->restrict = $restrict;
        $this->paymentHelper = $paymentHelper;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @param Config $subject
     * @param $paymentCode
     * @param $result
     * @return mixed
     */
    public function afterIsMethodAvailable(
        Config $subject,
        $result,
        $paymentCode = null
    ) {
        if ($this->productMetadata->getVersion() <= '2.1.11') {
            $paymentCode = $this->methodCode;
            $this->methodCode = null;
        }

        if ($paymentCode && in_array($paymentCode, $this->paypalExpressMethods) && $result) {
            $paymentCode = Config::METHOD_EXPRESS;
            $quote = $this->checkoutSession->getQuote();

            if (!$quote) {
                return $this;
            }

            $paypalPaymentMethodInstance = $this->paymentHelper->getMethodInstance($paymentCode);

            if (!$this->restrict->restrictMethods([$paypalPaymentMethodInstance], $quote)) {
                return false;
            }
        }

        return $result;
    }

    /**
     * @param Config $subject
     * @param $paymentCode
     * @return mixed
     */
    public function beforeIsMethodAvailable(Config $subject, $paymentCode = null)
    {
        if ($this->productMetadata->getVersion() <= '2.1.11') {
            $this->methodCode = $paymentCode;
        }

        return [$paymentCode];
    }
}
