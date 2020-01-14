<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoXTemplates\Model\Template\CategoryFilter\Source;

use MageWorx\SeoXTemplates\Model\Template\CategoryFilter as CategoryFilterTemplate;

/**
 * Used in creating options for config value selection
 *
 */
class AttributeCode
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toArray()
    {
        return [
            CategoryFilterTemplate::TYPE_CATEGORY_DESCRIPTION       => ['description'],
            CategoryFilterTemplate::TYPE_CATEGORY_META_TITLE        => ['meta_title'],
            CategoryFilterTemplate::TYPE_CATEGORY_META_DESCRIPTION  => ['meta_description'],
            CategoryFilterTemplate::TYPE_CATEGORY_META_KEYWORDS     => ['meta_keywords'],
            CategoryFilterTemplate::TYPE_CATEGORY_NAME              => ['category_seo_name'],
        ];
    }
}
