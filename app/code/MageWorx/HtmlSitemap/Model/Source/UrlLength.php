<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\HtmlSitemap\Model\Source;

/**
 * Using for sitemap categories and products sort order
 */
class UrlLength implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Sort order by position
     */
    const USE_CATEGORIES_PATH = 1;

    /**
     * Sort order by name
     */
    const USE_ROOT = 2;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::USE_CATEGORIES_PATH, 'label' => __('Use Categories Path')],
            ['value' => self::USE_ROOT,            'label' => __('Root')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::USE_CATEGORIES_PATH => __('Use Categories Path'),
            self::USE_ROOT            => __('Root')
        ];
    }
}
