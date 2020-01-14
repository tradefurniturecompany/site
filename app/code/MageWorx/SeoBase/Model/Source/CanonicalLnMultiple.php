<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\Source;

use MageWorx\SeoBase\Model\Canonical\Category as CategoryCanonical;

class CanonicalLnMultiple implements \Magento\Framework\Option\ArrayInterface
{
    /**
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => CategoryCanonical::CATEGORY_LN_CANONICAL_MULTIPLE_SELECTION_FILTERED,
                'label' => __('Filtered Page')
            ],
            [
                'value' => CategoryCanonical::CATEGORY_LN_CANONICAL_MULTIPLE_SELECTION_CATEGORY,
                'label' => __('Current Category')
            ],
        ];
    }
}