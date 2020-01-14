<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Model\Redirect\Source;

use MageWorx\SeoRedirects\Model\Redirect\DpRedirect;

class RedirectType extends \MageWorx\SeoRedirects\Model\Source
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
                'value' => DpRedirect::CODE_MOVED_PERMANENTLY,
                'label' => DpRedirect::CODE_MOVED_PERMANENTLY . ' ' . __('Moved Permanently')
            ],
            [
                'value' => DpRedirect::CODE_FOUND,
                'label' => DpRedirect::CODE_FOUND . ' ' . __('Found')
            ],
        ];
    }
}
