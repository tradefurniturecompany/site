<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\Source\CustomCanonical;

class CanonicalUrlType extends \MageWorx\SeoAll\Model\Source
{
    /**
     * @var int
     */
    const TYPE_DEFAULT  = 0;

    /**
     * @var int
     */
    const TYPE_CUSTOM  = 1;

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $data = [
            [
                'value' => 0,
                'label' => __('Use Default')
            ],
            [
                'value' => 1,
                'label' => __('Use Custom')
            ]
        ];

        return $data;
    }
}
