<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\HtmlSitemap\Model\Source;

/**
 * Used in creating options for Add|Crop config value selection
 *
 */
class AddCrop implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Value for crop slash
     */
    const TRAILING_SLASH_CROP = 0;

    /**
     * Value for add slash
     */
    const TRAILING_SLASH_ADD  = 1;

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
        return [self::TRAILING_SLASH_CROP => __('Crop'), self::TRAILING_SLASH_ADD => __('Add')];
    }
}
