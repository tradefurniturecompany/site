<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Template\LandingPage\Source;

use MageWorx\SeoXTemplates\Model\Template\LandingPage as LandingPageTemplate;
use MageWorx\SeoAll\Model\Source;

/**
 * Used in creating options for config value selection
 *
 */
class AssignType extends Source
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
                'value' => LandingPageTemplate::ASSIGN_ALL_ITEMS,
                'label' => __('All Landing Pages')
            ],
            [
                'value' => LandingPageTemplate::ASSIGN_INDIVIDUAL_ITEMS,
                'label' => __('Specific Landing Pages')
            ],
        ];
    }
}
