<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCrossLinks\Model\Crosslink\Source;

use MageWorx\SeoCrossLinks\Model\Source;
use MageWorx\SeoCrossLinks\Model\Crosslink;

class Target extends Source
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
                'value' => Crosslink::TARGET_LINK_SELF,
                'label' => __('_self')
            ],[
                'value' => Crosslink::TARGET_LINK_BLANK,
                'label' => __('_blank')
            ],
        ];
    }
}
