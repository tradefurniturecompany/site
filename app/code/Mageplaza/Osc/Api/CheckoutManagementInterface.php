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

namespace Mageplaza\Osc\Api;

/**
 * Interface for update item information
 * @api
 */
interface CheckoutManagementInterface
{
    /**
     * @param int $cartId
     * @param int $itemId
     * @param int $itemQty
     *
     * @return \Mageplaza\Osc\Api\Data\OscDetailsInterface
     */
    public function updateItemQty($cartId, $itemId, $itemQty);

    /**
     * @param int $cartId
     * @param int $itemId
     *
     * @return \Mageplaza\Osc\Api\Data\OscDetailsInterface
     */
    public function removeItemById($cartId, $itemId);

    /**
     * @param int $cartId
     *
     * @return \Mageplaza\Osc\Api\Data\OscDetailsInterface
     */
    public function getPaymentTotalInformation($cartId);

    /**
     * @param int $cartId
     * @param bool $isUseGiftWrap
     *
     * @return \Mageplaza\Osc\Api\Data\OscDetailsInterface
     */
    public function updateGiftWrap($cartId, $isUseGiftWrap);

    /**
     * @param int $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @param string[] $customerAttributes
     * @param string[] $additionInformation
     *
     * @return bool
     */
    public function saveCheckoutInformation(
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation,
        $customerAttributes = [],
        $additionInformation = []
    );

    /**
     * Estimate checkout information on the change of the address
     *
     * @param int $cartId The shopping cart ID.
     * @param int $addressId The estimate address id
     * @param bool $isAddressSameAsShipping
     *
     * @return \Mageplaza\Osc\Api\Data\OscDetailsInterface
     */
    public function estimateByAddressId($cartId, $addressId, $isAddressSameAsShipping);

    /**
     * Estimate checkout information for the new address
     *
     * @param int $cartId The shopping cart ID.
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     * @param bool $isAddressSameAsShipping
     *
     * @return \Mageplaza\Osc\Api\Data\OscDetailsInterface
     */
    public function estimateByExtendedAddress($cartId, $address, $isAddressSameAsShipping);
}
