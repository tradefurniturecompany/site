<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Model\Rule\Factory;

/**
 * Interface HandleFactoryInterface
 */
interface HandleFactoryInterface
{
    const CUSTOMER_HANDLE = 'customer';
    const ORDERS_HANDLE = 'orders';

    const TOTAL_COMBINE_HANDLE = 'total';

    /**
     * @param string $type
     * @return array
     */
    public function create($type = self::CUSTOMER_HANDLE);
}

