<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Template\Product\Source;

use MageWorx\SeoXTemplates\Model\Template\Product as ProductTemplate;
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
                'value' => ProductTemplate::ASSIGN_ALL_ITEMS,
                'label' => __('All Products')
            ],
            [
                'value' => ProductTemplate::ASSIGN_GROUP_ITEMS,
                'label' => __('By Attribute Set')
            ],
            [
                'value' => ProductTemplate::ASSIGN_INDIVIDUAL_ITEMS,
                'label' => __('Specific Products')
            ],
        ];
    }
}
