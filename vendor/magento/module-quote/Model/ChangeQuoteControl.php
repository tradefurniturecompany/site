<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Quote\Model;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Quote\Api\ChangeQuoteControlInterface;
use Magento\Quote\Api\Data\CartInterface;

/**
 * {@inheritdoc}
 */
class ChangeQuoteControl implements ChangeQuoteControlInterface
{
    /**
     * @var UserContextInterface $userContext
     */
    private $userContext;

    /**
     * @param UserContextInterface $userContext
     */
    public function __construct(UserContextInterface $userContext)
    {
        $this->userContext = $userContext;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowed(CartInterface $quote): bool
    {
        switch ($this->userContext->getUserType()) {
            case UserContextInterface::USER_TYPE_CUSTOMER:
                $isAllowed = ($quote->getCustomerId() == $this->userContext->getUserId());
                break;
            case UserContextInterface::USER_TYPE_GUEST:
                $isAllowed = ($quote->getCustomerId() === null);
                break;
            case UserContextInterface::USER_TYPE_ADMIN:
            case UserContextInterface::USER_TYPE_INTEGRATION:
                $isAllowed = true;
                break;
            default:
                $isAllowed = false;
        }
        # 2020-06-30 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		# «Invalid state change requested
		# at vendor/magento/module-quote/Model/QuoteRepository/Plugin/AccessChangeQuoteControl.php:45»:
		# https://github.com/tradefurniturecompany/site/issues/171
		if (!$isAllowed) {
			df_log_l($this, ['getCustomerId' => $quote->getCustomerId(), 'getUserType' => $this->userContext->getUserType()]);
		}
        return $isAllowed;
    }
}
