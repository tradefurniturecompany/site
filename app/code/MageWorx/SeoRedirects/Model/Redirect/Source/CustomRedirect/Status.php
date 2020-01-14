<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Model\Redirect\Source\CustomRedirect;

use MageWorx\SeoRedirects\Model\Redirect\CustomRedirect;

class Status extends \MageWorx\SeoAll\Model\Source
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
                'value' => CustomRedirect::STATUS_ENABLED,
                'label' => __('Enable')
            ],
            [
                'value' => CustomRedirect::STATUS_DISABLED,
                'label' => __('Disable')
            ],
        ];
    }
}
