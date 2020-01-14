<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Model\Source;

use MageWorx\SeoExtended\Model\Source;

class SingleFilter extends Source
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Use by Attribute Position')],
            ['value' => 1, 'label' => __('Do not use')]
        ];
    }
}