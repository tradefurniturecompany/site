<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Template\Source;

use MageWorx\SeoXTemplates\Model\AbstractTemplate as Template;
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
                'value' => Template::SCOPE_EMPTY,
                'label' => __('Empty')
            ],
            [
                'value' => Template::SCOPE_ALL,
                'label' => __('All')
            ],
        ];
    }
}
