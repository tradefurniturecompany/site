<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBreadcrumbs\Model\Source;

use MageWorx\SeoBreadcrumbs\Model\Source;

/**
 * Used in creating options for default destination config value selection
 *
 */
class Type extends Source
{
    const BREADCRUMBS_TYPE_DEFAULT   = 0;
    const BREADCRUMBS_TYPE_SHORTEST  = 1;
    const BREADCRUMBS_TYPE_LONGEST   = 2;

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::BREADCRUMBS_TYPE_DEFAULT,  'label' => __('Default')],
            ['value' => self::BREADCRUMBS_TYPE_SHORTEST, 'label' => __('Use Shortest')],
            ['value' => self::BREADCRUMBS_TYPE_LONGEST,  'label' => __('Use Longest')]
        ];
    }
}
