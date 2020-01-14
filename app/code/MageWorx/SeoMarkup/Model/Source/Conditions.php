<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoMarkup\Model\Source;

/**
 * Used in creating options for config value selection
 *
 */
class Conditions extends \MageWorx\SeoMarkup\Model\Source
{
    /**
     *
     * {@inheritDoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'NewCondition',         'label' => 'New'],
            ['value' => 'RefurbishedCondition', 'label' => 'Refurbished'],
            ['value' => 'UsedCondition',        'label' => 'Used'],
            ['value' => 'DamagedCondition',     'label' => 'Damaged'],
        ];
    }
}
