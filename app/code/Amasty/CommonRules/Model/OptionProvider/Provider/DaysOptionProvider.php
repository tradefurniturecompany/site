<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Model\OptionProvider\Provider;

class DaysOptionProvider implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var array|null
     */
    protected $options;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = [
                [
                    'value' => '7',
                    'label' => __('Sunday')
                ],
                [
                    'value' => '1',
                    'label' => __('Monday')
                ],
                [
                    'value' => '2',
                    'label' => __('Tuesday')
                ],
                [
                    'value' => '3',
                    'label' => __('Wednesday')
                ],
                [
                    'value' => '4',
                    'label' => __('Thursday')
                ],
                [
                    'value' => '5',
                    'label' => __('Friday')
                ],
                [
                    'value' => '6',
                    'label' => __('Saturday')
                ],
            ];
        }

        return $this->options;
    }
}
