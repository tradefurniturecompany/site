<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Api\Data;

/**
 * Interface ShippingMethodInterface
 * @api
 */
interface AddressInterface extends \Magento\Quote\Api\Data\AddressInterface
{
    /**
     * Sets the shipping carrier comment.
     *
     * @param string $comment
     * @return \Amasty\ShippingTableRates\Api\Data\AddressInterface
     */
    public function setComment($comment);

    /**
     * Sets the shipping carrier comment.
     *
     * @return \Amasty\ShippingTableRates\Api\Data\AddressInterface
     */
    public function getComment();
}
