<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCrossLinks\Model\Config\Source;

use MageWorx\SeoCrossLinks\Model\Source;

/**
 * Used in creating options for config value selection
 *
 */
class ProductAttributes extends Source
{
    /**
     * Attribute code for short description
     */
    const SHORT_DESCRIPTION_CODE = 'short_description';

    /**
     * Attribute code for description
     */
    const DESCRIPTION_CODE       = 'description';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::SHORT_DESCRIPTION_CODE, 'label' => __('Product Short Description')],
            ['value' => self::DESCRIPTION_CODE,       'label' => __('Product Description')],
        ];
    }
}
