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

namespace Mageplaza\Osc\Model\Plugin\Customer;

use Closure;
use Magento\Customer\Api\Data\AddressInterface;

/**
 * Class Address
 * @package Mageplaza\Osc\Model\Plugin\Customer
 */
class Address
{
    /**
     * @param \Magento\Customer\Model\Address $subject
     * @param Closure $proceed
     * @param AddressInterface $address
     *
     * @return mixed
     */
    public function aroundUpdateData(
        \Magento\Customer\Model\Address $subject,
        Closure $proceed,
        AddressInterface $address
    ) {
        $object = $proceed($address);

        $addressData = $address->__toArray();
        if (isset($addressData['should_ignore_validation'])) {
            $object->setShouldIgnoreValidation($addressData['should_ignore_validation']);
        }

        return $object;
    }
}
