<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Template\Category\Source;

use MageWorx\SeoXTemplates\Model\Template\Category as CategoryTemplate;
use MageWorx\SeoXTemplates\Model\Source;

/**
 * Used in creating options for config value selection
 *
 */
class AssignType extends Source
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => CategoryTemplate::ASSIGN_ALL_ITEMS,
                'label' => __('All Categories')
            ],
            [
                'value' => CategoryTemplate::ASSIGN_INDIVIDUAL_ITEMS,
                'label' => __('Specific Categories')
            ],
        ];
    }
}
