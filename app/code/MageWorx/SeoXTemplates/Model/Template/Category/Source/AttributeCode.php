<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Template\Category\Source;

use MageWorx\SeoXTemplates\Model\Template\Category as CategoryTemplate;

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
            CategoryTemplate::TYPE_CATEGORY_SEO_NAME          => ['category_seo_name'],
            CategoryTemplate::TYPE_CATEGORY_DESCRIPTION       => ['description'],
            CategoryTemplate::TYPE_CATEGORY_META_TITLE        => ['meta_title'],
            CategoryTemplate::TYPE_CATEGORY_META_DESCRIPTION  => ['meta_description'],
            CategoryTemplate::TYPE_CATEGORY_META_KEYWORDS     => ['meta_keywords'],
        ];
    }
}
