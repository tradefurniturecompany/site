<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoUrls\Model\Source;

/**
 * Used in creating options for config value selection
 *
 */
class PagerMask extends \MageWorx\SeoUrls\Model\Source
{
    const PAGER_NUM_MASK = '[pager_num]';
    const PAGER_VAR_MASK = '[pager_var]';

    /**
     *
     * {@inheritDoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => '-' . self::PAGER_VAR_MASK . self::PAGER_NUM_MASK,
                'label' => '-' . self::PAGER_VAR_MASK . self::PAGER_NUM_MASK
            ],
            [
                'value' => '/' . self::PAGER_VAR_MASK . '/' . self::PAGER_NUM_MASK,
                'label' => '/' . self::PAGER_VAR_MASK . '/' . self::PAGER_NUM_MASK

            ]
        ];
    }
}
