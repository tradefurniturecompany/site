<?php

namespace IWD\InfinityScroll\Model\Config\Source;

use \Magento\Framework\Option\ArrayInterface;

/**
 * Class Mode
 * @package IWD\InfinityScroll\Model\Config\Source
 */
class Mode implements ArrayInterface
{
    const MODE_SCROLL = 'scroll';
    const MODE_BUTTON = 'button';
    const MODE_PAGINATION = 'pagination';

    /**
     * Options getter
     *
     * @return string[]
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'button',
                'label' => __('Upload After Button Click')
            ], [
                'value' => 'scroll',
                'label' => __('AJAX-Scroll Mode')
            ], [
                'value' => 'pagination',
                'label' => __('AJAX-Pagination Mode')
            ],
        ];
    }
}
