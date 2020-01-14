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

namespace Mageplaza\DeliveryTime\Model\Plugin\Checkout;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Mageplaza\DeliveryTime\Helper\Data as MpDtHelper;

/**
 * Class ShippingInformationManagement
 * @package Mageplaza\DeliveryTime\Model\Plugin\Checkout
 */
class ShippingInformationManagement
{
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var MpDtHelper
     */
    protected $mpDtHelper;

    /**
     * @param CheckoutSession $checkoutSession
     * @param MpDtHelper $mpDtHelper
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        MpDtHelper $mpDtHelper
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->mpDtHelper = $mpDtHelper;
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param int $cartId
     * @param ShippingInformationInterface $addressInformation
     *
     * @return array
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        if ($this->mpDtHelper->isEnabled() && $extensionAttributes = $addressInformation->getShippingAddress()->getExtensionAttributes()) {
            $deliveryInformation = [
                'deliveryDate'      => $extensionAttributes->getMpDeliveryDate(),
                'deliveryTime'      => $extensionAttributes->getMpDeliveryTime(),
                'houseSecurityCode' => $extensionAttributes->getMpHouseSecurityCode(),
                'deliveryComment'   => $extensionAttributes->getMpDeliveryComment()
            ];
            $this->checkoutSession->setMpdtData($deliveryInformation);
        }

        return [$cartId, $addressInformation];
    }
}
