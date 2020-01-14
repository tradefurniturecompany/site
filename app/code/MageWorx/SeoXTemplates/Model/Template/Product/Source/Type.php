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
                'value' => ProductTemplate::TYPE_PRODUCT_SEO_NAME,
                'label' => __('Product SEO Name')
            ],
            [
                'value' => ProductTemplate::TYPE_PRODUCT_URL_KEY,
                'label' => __('Product URL Key')
            ],
            [
                'value' => ProductTemplate::TYPE_PRODUCT_SHORT_DESCRIPTION,
                'label' => __('Product Short Description')
            ],
            [
                'value' => ProductTemplate::TYPE_PRODUCT_DESCRIPTION,
                'label' => __('Product Description')
            ],
            [
                'value' => ProductTemplate::TYPE_PRODUCT_META_TITLE,
                'label' => __('Product Meta Title')
            ],
            [
                'value' => ProductTemplate::TYPE_PRODUCT_META_DESCRIPTION,
                'label' => __('Product Meta Description')
            ],
            [
                'value' => ProductTemplate::TYPE_PRODUCT_META_KEYWORDS,
                'label' => __('Product Meta Keywords')
            ],
            [
                'value' => ProductTemplate::TYPE_PRODUCT_GALLERY,
                'label' => __('Product Image Alt Text')
            ],
        ];
    }
}
