<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

/**
 * Used in creating options for Add|Crop config value selection
 *
 */
namespace MageWorx\SeoBase\Model\Source;

class AddCrop implements \Magento\Framework\Option\ArrayInterface
{
    const TRAILING_SLASH_ADD  = 1;

    const TRAILING_SLASH_CROP = 0;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::TRAILING_SLASH_ADD, 'label' => __('Add')],
            ['value' => self::TRAILING_SLASH_CROP, 'label' => __('Crop')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [0 => __('Crop'), 1 => __('Add')];
    }
}
