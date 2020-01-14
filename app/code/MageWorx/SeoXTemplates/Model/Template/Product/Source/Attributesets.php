<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Template\Product\Source;

use Magento\Catalog\Model\Product\AttributeSet\Options as AttributeSetOptions;
use MageWorx\SeoXTemplates\Model\Template\Product as ProductTemplate;
use MageWorx\SeoXTemplates\Model\Source;

/**
 * Used in creating options for config value selection
 *
 */
class Attributesets extends AttributeSetOptions
{
    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray($withEmpty = true)
    {
        $_tmpOptions = $this->toOptionArray();

        $_options = [];
        if ($withEmpty) {
            array_unshift($_options, __('--Please Select--'));
        }

        foreach ($_tmpOptions as $option) {
            $_options[(string)$option['value']] = $option['label'];
        }

        return $_options;
    }
}
