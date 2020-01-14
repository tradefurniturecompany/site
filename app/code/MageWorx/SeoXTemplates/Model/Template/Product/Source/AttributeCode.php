<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Template\Product\Source;

use MageWorx\SeoXTemplates\Model\Template\Product as ProductTemplate;

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
            ProductTemplate::TYPE_PRODUCT_SEO_NAME          => ['product_seo_name'],
            ProductTemplate::TYPE_PRODUCT_URL_KEY           => ['url_key'],
            ProductTemplate::TYPE_PRODUCT_SHORT_DESCRIPTION => ['short_description'],
            ProductTemplate::TYPE_PRODUCT_DESCRIPTION       => ['description'],
            ProductTemplate::TYPE_PRODUCT_META_TITLE        => ['meta_title'],
            ProductTemplate::TYPE_PRODUCT_META_DESCRIPTION  => ['meta_description'],
            ProductTemplate::TYPE_PRODUCT_META_KEYWORDS     => ['meta_keyword'],
            ProductTemplate::TYPE_PRODUCT_GALLERY           => ['media_gallery']
        ];
    }
}
