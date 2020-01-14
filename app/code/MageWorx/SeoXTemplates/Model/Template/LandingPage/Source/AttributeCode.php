<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Template\LandingPage\Source;

use MageWorx\SeoXTemplates\Model\Template\LandingPage as LandingPageTemplate;

/**
 * Used in creating options for config value selection
 *
 */
class AttributeCode
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toArray()
    {
        return [
            LandingPageTemplate::TYPE_LANDING_PAGE_HEADER           => ['header'],
            LandingPageTemplate::TYPE_LANDING_PAGE_META_TITLE       => ['meta_title'],
            LandingPageTemplate::TYPE_LANDING_PAGE_META_DESCRIPTION => ['meta_description'],
            LandingPageTemplate::TYPE_LANDING_PAGE_META_KEYWORDS    => ['meta_keywords'],
            LandingPageTemplate::TYPE_LANDING_PAGE_URL_KEY          => ['url_key'],
            LandingPageTemplate::TYPE_LANDING_PAGE_TEXT_1           => ['text_1'],
            LandingPageTemplate::TYPE_LANDING_PAGE_TEXT_2           => ['text_2'],
            LandingPageTemplate::TYPE_LANDING_PAGE_TEXT_3           => ['text_3'],
            LandingPageTemplate::TYPE_LANDING_PAGE_TEXT_4           => ['text_4']
        ];
    }
}
