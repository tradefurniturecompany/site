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
class Type extends Source
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
                'value' => CategoryTemplate::TYPE_CATEGORY_SEO_NAME,
                'label' => __('Category SEO Name')
            ],
            [
                'value' => CategoryTemplate::TYPE_CATEGORY_DESCRIPTION,
                'label' => __('Category Description')
            ],
            [
                'value' => CategoryTemplate::TYPE_CATEGORY_META_TITLE,
                'label' => __('Category Meta Title')
            ],
            [
                'value' => CategoryTemplate::TYPE_CATEGORY_META_DESCRIPTION,
                'label' => __('Category Meta Description')
            ],
            [
                'value' => CategoryTemplate::TYPE_CATEGORY_META_KEYWORDS,
                'label' => __('Category Meta Keywords')
            ],
        ];
    }
}
