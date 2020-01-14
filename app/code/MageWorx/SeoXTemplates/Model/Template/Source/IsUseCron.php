<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Template\Source;

use MageWorx\SeoXTemplates\Model\AbstractTemplate as Template;
use MageWorx\SeoXTemplates\Model\Source;

/**
 * Used in creating options for config value selection
 *
 */
class IsUseCron extends Source
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
                'value' => Template::CRON_DISABLED,
                'label' => __('No')
            ],
            [
                'value' => Template::CRON_ENABLED,
                'label' => __('Yes')
            ],
        ];
    }
}
