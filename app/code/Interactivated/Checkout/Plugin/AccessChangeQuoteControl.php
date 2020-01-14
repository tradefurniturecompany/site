<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Interactivated\Checkout\Plugin;

use Magento\Quote\Api\ChangeQuoteControlInterface;
use Magento\Framework\Exception\StateException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;

/**
 * The plugin checks if the user has ability to change the quote.
 */
class AccessChangeQuoteControl
{
    /**
     * Checks if change quote's customer id is allowed for current user.
     *
     * @param CartRepositoryInterface $subject
     * @param CartInterface $quote
     * @throws StateException if Guest has customer_id or Customer's customer_id not much with user_id
     * or unknown user's type
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSave(CartRepositoryInterface $subject, CartInterface $quote)
    {
        if ($quote->getCustomerIsGuest() && $quote->getCustomerId()){
            $quote->setCustomerIsGuest(false);
        }
    }
}
