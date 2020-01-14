<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class MenuWidth implements OptionSourceInterface
{
    const FULL = 0;

    const AUTO = 1;

    const CUSTOM = 2;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::FULL, 'label' => __('Full Width')],
            ['value' => self::AUTO, 'label' => __('Auto')],
            ['value' => self::CUSTOM, 'label' => __('Custom')]
        ];
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getLabelByValue($value)
    {
        foreach ($this->toOptionArray() as $item) {
            if ($item['value'] == $value) {
                return $item['label'];
            }
        }

        return '';
    }
}
