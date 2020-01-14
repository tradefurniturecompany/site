<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\Source;

use MageWorx\SeoBase\Model\Source;

/**
 * Used in creating options for default destination config value selection
 *
 */
class Scope extends Source
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            ['value' => '0', 'label' => __('Global')],
            ['value' => '1', 'label' => __('Website')]
        ];
    }
}
