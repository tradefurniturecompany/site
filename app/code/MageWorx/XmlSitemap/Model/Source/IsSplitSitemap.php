<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Model\Source;

use MageWorx\SeoAll\Model\Source;

class IsSplitSitemap extends Source
{
    const SPLIT_SITEMAP_DISABLED = 0;
    const SPLIT_SITEMAP_ENABLED  = 1;

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::SPLIT_SITEMAP_DISABLED,
                'label' => __('No')
            ],
            [
                'value' => self::SPLIT_SITEMAP_ENABLED,
                'label' => __('Yes')
            ],
        ];
    }
}
