<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Model\Redirect\Source;

use MageWorx\SeoRedirects\Model\Source;
use MageWorx\SeoRedirects\Model\Redirect\DpRedirect;

class RedirectTarget extends Source
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
                'value' => DpRedirect::TARGET_SELF_CATEGORY,
                'label' => __('Product Category')
            ],
            [
                'value' => DpRedirect::TARGET_PRIORITY_CATEGORY,
                'label' => __('Priority Category')
            ],
        ];
    }
}
