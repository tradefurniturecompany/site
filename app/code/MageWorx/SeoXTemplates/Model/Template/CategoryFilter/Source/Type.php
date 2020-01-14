<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoXTemplates\Model\Template\CategoryFilter\Source;

use MageWorx\SeoXTemplates\Model\Template\CategoryFilter as CategoryFilterTemplate;
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
                'value' => CategoryFilterTemplate::TYPE_CATEGORY_DESCRIPTION,
                'label' => __('Category Filter Description')
            ],
            [
                'value' => CategoryFilterTemplate::TYPE_CATEGORY_META_TITLE,
                'label' => __('Category Filter Meta Title')
            ],
            [
                'value' => CategoryFilterTemplate::TYPE_CATEGORY_META_DESCRIPTION,
                'label' => __('Category Filter Meta Description')
            ],
            [
                'value' => CategoryFilterTemplate::TYPE_CATEGORY_META_KEYWORDS,
                'label' => __('Category Filter Meta Keywords')
            ],
            [
                'value' => CategoryFilterTemplate::TYPE_CATEGORY_NAME,
                'label' => __('Category SEO Name')
            ],
        ];
    }
}
