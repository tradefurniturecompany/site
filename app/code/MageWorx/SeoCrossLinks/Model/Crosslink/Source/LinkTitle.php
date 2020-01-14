<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCrossLinks\Model\Crosslink\Source;

use MageWorx\SeoCrossLinks\Model\Crosslink;
use MageWorx\SeoCrossLinks\Model\Source;

/**
 * Used in creating options for config value selection
 *
 */
class LinkTitle extends Source
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            ['value' => Crosslink::USE_CROSSLINK_TITLE_ONLY, 'label' => __("Don't Use")],
            ['value' => Crosslink::USE_NAME_IF_EMPTY_TITLE,  'label' => __('For Blank')],
            ['value' => Crosslink::USE_NAME_ALWAYS,          'label' => __('For All')],
        ];
    }
}
