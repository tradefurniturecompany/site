<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\Source;

use MageWorx\SeoAll\Model\Source;

class CanonicalType extends Source
{
    const URL_TYPE_LONGEST            = 'canonical_type_longest';
    const URL_TYPE_SHORTEST           = 'canonical_type_shortest';
    const URL_TYPE_MAX_CATEGORY_LEVEL = 'canonical_type_max_category_level';
    const URL_TYPE_MIN_CATEGORY_LEVEL = 'canonical_type_min_category_level';
    const URL_TYPE_NO_CATEGORIES      = 'canonical_type_root';

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::URL_TYPE_LONGEST, 'label' => __('Use Longest by Path Length')],
            ['value' => self::URL_TYPE_SHORTEST, 'label' => __('Use Shortest by Path Length')],
            ['value' => self::URL_TYPE_MAX_CATEGORY_LEVEL, 'label' => __('Use Longest by Categories Counter')],
            ['value' => self::URL_TYPE_MIN_CATEGORY_LEVEL, 'label' => __('Use Shortest by Categories Counter')],
            ['value' => self::URL_TYPE_NO_CATEGORIES, 'label' => __('Use Root (without Categories)')]
        ];
    }
}
