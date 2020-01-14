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
class Type extends Source
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
                'value' => LandingPageTemplate::TYPE_LANDING_PAGE_HEADER,
                'label' => __('Landing Page Header')
            ],
            [
                'value' => LandingPageTemplate::TYPE_LANDING_PAGE_META_TITLE,
                'label' => __('Landing Page Meta Title')
            ],
            [
                'value' => LandingPageTemplate::TYPE_LANDING_PAGE_META_DESCRIPTION,
                'label' => __('Landing Page Meta Description')
            ],
            [
                'value' => LandingPageTemplate::TYPE_LANDING_PAGE_META_KEYWORDS,
                'label' => __('Landing Page Meta Keywords')
            ],
            [
                'value' =>  LandingPageTemplate::TYPE_LANDING_PAGE_URL_KEY,
                'label' => __('Landing Page Url Key')
            ],
            [
                'value' => LandingPageTemplate::TYPE_LANDING_PAGE_TEXT_1,
                'label' => __('Landing Page Text #1')
            ],
            [
                'value' => LandingPageTemplate::TYPE_LANDING_PAGE_TEXT_2,
                'label' => __('Landing Page Text #2')
            ],
            [
                'value' => LandingPageTemplate::TYPE_LANDING_PAGE_TEXT_3,
                'label' => __('Landing Page Text #3')
            ],
            [
                'value' => LandingPageTemplate::TYPE_LANDING_PAGE_TEXT_4,
                'label' => __('Landing Page Text #4')
            ],
        ];
    }
}
