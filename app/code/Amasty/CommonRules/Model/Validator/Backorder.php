<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Model\Validator;

use Amasty\CommonRules\Model\Rule;

class Backorder implements \Amasty\CommonRules\Model\Validator\ValidatorInterface
{
    /**
     * @inheritdoc
     */
    public function validate($rule, $items)
    {
        $hasBackOrders = $this->hasBackorders($items);
        $hasNonBackOrders = $this->hasNonBackorders($items);

        switch ($rule->getOutOfStock()) {
            case Rule::BACKORDERS_ONLY:
                $validBackOrder = $hasBackOrders && !$hasNonBackOrders;
                break;
            case Rule::NON_BACKORDERS:
                $validBackOrder = !$hasBackOrders && $hasNonBackOrders;
                break;
            default:
                $validBackOrder = true;
        }

        return $validBackOrder;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item[] $items
     *
     * @return bool
     */
    protected function hasBackorders($items)
    {
        foreach ($items as $item) {
            if ($item->getBackorders() > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item[] $items
     *
     * @return bool
     */
    protected function hasNonBackorders($items)
    {
        foreach ($items as $item) {
            if ($item->getBackorders() == 0) {
                return true;
            }
        }

        return false;
    }
}
