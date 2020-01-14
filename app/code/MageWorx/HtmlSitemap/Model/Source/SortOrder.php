<?php
/**
 * MageWorx
 * MageWorx HtmlSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_HtmlSitemap
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
namespace MageWorx\HtmlSitemap\Model\Source;

/**
 * Using for sitemap categories and products sort order
 */
class SortOrder implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Sort order by position
     */
    const SORT_BY_POSITION = 1;

    /**
     * Sort order by name
     */
    const SORT_BY_NAME = 2;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::SORT_BY_POSITION, 'label' => __('Position')],
            ['value' => self::SORT_BY_NAME,     'label' => __('Name')]
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
            self::SORT_BY_POSITION => __('Position'),
            self::SORT_BY_NAME     => __('Name')
        ];
    }
}
