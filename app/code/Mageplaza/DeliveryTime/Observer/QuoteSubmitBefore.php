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
 * @package     Mageplaza_DeliveryTime
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\DeliveryTime\Observer;

use Magento\Checkout\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mageplaza\DeliveryTime\Helper\Data as MpDtHelper;

/**
 * Class QuoteSubmitBefore
 * @package Mageplaza\DeliveryTime\Observer
 */
class QuoteSubmitBefore implements ObserverInterface
{
    /**
     * @var MpDtHelper
     */
    protected $mpDtHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @param MpDtHelper $mpDtHelper
     * @param Session $checkoutSession
     *
     * @codeCoverageIgnore
     */
    public function __construct(
        MpDtHelper $mpDtHelper,
        Session $checkoutSession
    ) {
        $this->mpDtHelper = $mpDtHelper;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Api\Data\OrderInterface $order */
        $order = $observer->getEvent()->getOrder();

        if ($mpDtData = $this->checkoutSession->getMpdtData()) {
            $order->setData('mp_delivery_information', json_encode($mpDtData));

            $this->checkoutSession->unsMpdtData();
        }
    }
}
