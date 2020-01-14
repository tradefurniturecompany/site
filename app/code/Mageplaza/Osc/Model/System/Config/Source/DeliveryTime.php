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

namespace Mageplaza\Osc\Model\System\Config\Source;

use Magento\Framework\Model\AbstractModel;

/**
 * Class DeliveryTime
 * @package Mageplaza\Osc\Model\System\Config\Source
 */
class DeliveryTime extends AbstractModel
{
    const DAY_MONTH_YEAR = 'dd/mm/yy';
    const MONTH_DAY_YEAR = 'mm/dd/yy';
    const YEAR_MONTH_DAY = 'yy/mm/dd';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'label' => __('Day/Month/Year'),
                'value' => self::DAY_MONTH_YEAR
            ],
            [
                'label' => __('Month/Day/Year'),
                'value' => self::MONTH_DAY_YEAR
            ],
            [
                'label' => __('Year/Month/Day'),
                'value' => self::YEAR_MONTH_DAY
            ]
        ];

        return $options;
    }
}