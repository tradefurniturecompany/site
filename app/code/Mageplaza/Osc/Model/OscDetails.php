<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Osc
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Osc\Model;

use Magento\Framework\Model\AbstractExtensibleModel;
use Mageplaza\Osc\Api\Data\OscDetailsInterface;

/**
 * @codeCoverageIgnoreStart
 */
class OscDetails extends AbstractExtensibleModel implements OscDetailsInterface
{
    /**
     * @{inheritdoc}
     */
    public function getShippingMethods()
    {
        return $this->getData(self::SHIPPING_METHODS);
    }

    /**
     * @{inheritdoc}
     */
    public function setShippingMethods($shippingMethods)
    {
        return $this->setData(self::SHIPPING_METHODS, $shippingMethods);
    }

    /**
     * @{inheritdoc}
     */
    public function getPaymentMethods()
    {
        return $this->getData(self::PAYMENT_METHODS);
    }

    /**
     * @{inheritdoc}
     */
    public function setPaymentMethods($paymentMethods)
    {
        return $this->setData(self::PAYMENT_METHODS, $paymentMethods);
    }

    /**
     * @{inheritdoc}
     */
    public function getTotals()
    {
        return $this->getData(self::TOTALS);
    }

    /**
     * @{inheritdoc}
     */
    public function setTotals($totals)
    {
        return $this->setData(self::TOTALS, $totals);
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->getData(self::REDIRECT_URL);
    }

    /**
     * @param $url
     *
     * @return $this
     */
    public function setRedirectUrl($url)
    {
        return $this->setData(self::REDIRECT_URL, $url);
    }
}
