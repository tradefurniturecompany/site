<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Model\Modifiers;

class Address implements \Amasty\CommonRules\Model\Modifiers\ModifierInterface
{
    /**
     * @param \Magento\Framework\DataObject $object
     * @param null $rateAddress
     * @return \Magento\Framework\DataObject
     */
    public function modify($object, $rateAddress  = null)
    {
        if ($rateAddress) {
            $object->setData($object->getData() + $rateAddress->getData());
        }

        return $object;
    }
}
