<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Model\OptionProvider\Provider;

class TimesOptionProvider implements \Magento\Framework\Data\OptionSourceInterface
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
            $timeArray = [
                ['value' => 0, 'label' => __('Please select...')]
            ];

            for ($i = 0; $i < 24; $i++) {
                for ($j = 0; $j < 60; $j = $j + 15) {
                    $timeStamp = $i . ':' . $j;
                    $timeFormat = date('H:i', strtotime($timeStamp));
                    $timeArray[] = ['value' => $i * 100 + $j + 1, 'label' => $timeFormat];
                }
            }

            $this->options = $timeArray;
        }

        return $this->options;
    }
}
