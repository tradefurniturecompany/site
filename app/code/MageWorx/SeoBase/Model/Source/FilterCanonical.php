<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\Source;

/**
 * Using for canonical URL on the layered navigation pages
 */
class FilterCanonical implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Use Config')],
            ['value' => 1, 'label' => __('Filtered Page')],
            ['value' => 2, 'label' => __('Current Category')]
        ];
    }
}
