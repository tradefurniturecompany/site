<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Model\OptionProvider\Provider;

class CalculationOptionProvider implements \Magento\Framework\Data\OptionSourceInterface
{
    const CALC_REPLACE = 0;
    const CALC_ADD     = 1;
    const CALC_DEDUCT  = 2;

    /**
     * @var array|null
     */
    protected $options;

    /**
     * @return array|null
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = [
                self::CALC_REPLACE  => __('Replace'),
                self::CALC_ADD      => __('Surcharge'),
                self::CALC_DEDUCT   => __('Discount'),
            ];
        }

        return $this->options;
    }
}
