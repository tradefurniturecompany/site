<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCrossLinks\Model\Crosslink\Source;

use MageWorx\SeoCrossLinks\Model\Crosslink;
use MageWorx\SeoCrossLinks\Model\Source;

class IsActive extends Source
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => Crosslink::STATUS_ENABLED,
                'label' => __('Yes')
            ],[
                'value' => Crosslink::STATUS_DISABLED,
                'label' => __('No')
            ],
        ];
    }
}
