<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Model\Redirect\Source;

use MageWorx\SeoRedirects\Model\Source;
use MageWorx\SeoRedirects\Model\Redirect\DpRedirect;

class Status extends Source
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
                'value' => DpRedirect::STATUS_DISABLED,
                'label' => __('Disabled')
            ],
            [
                'value' => DpRedirect::STATUS_ENABLED,
                'label' => __('Enabled')
            ],
        ];
    }
}
