<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Model\Source;

class AddPageNum extends \MageWorx\SeoExtended\Model\Source
{
    const PAGE_NUM_ADD_TO_BEINNING = 'beginning';
    const PAGE_NUM_ADD_TO_END      = 'end';
    const PAGE_NUM_NO_ADD          = 'off';

    /**
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::PAGE_NUM_ADD_TO_BEINNING,  'label' => __('At the Beginning')],
            ['value' => self::PAGE_NUM_ADD_TO_END,       'label' => __('At the End')],
            ['value' => self::PAGE_NUM_NO_ADD,           'label' => __('No')]
        ];
    }
}
