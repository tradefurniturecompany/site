<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\Source;

class CanonicalLn implements \Magento\Framework\Option\ArrayInterface
{
    /**
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Disable')],
            ['value' => 1, 'label' => __('Filtered Page')],
            ['value' => 2, 'label' => __('Current Category')]
        ];
    }
}
