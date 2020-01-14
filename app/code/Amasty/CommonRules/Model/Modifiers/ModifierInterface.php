<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Model\Modifiers;

/**
 * Interface ModifierInterface
 */
interface ModifierInterface
{
    /**
     * Modify Object
     * @param \Magento\Framework\DataObject $object
     * @return \Magento\Framework\DataObject
     */
    public function modify($object);
}
