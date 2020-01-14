<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoMarkup\Model\Source;

class WeightUnit extends \MageWorx\SeoMarkup\Model\Source
{
    /**
     *
     * {@inheritDoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'lb', 'label' => __('lb')],
            ['value' => 'kg', 'label' => __('kg')]
        ];
    }
}
