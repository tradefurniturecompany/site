<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCrossLinks\Model\Config\Source;

use MageWorx\SeoCrossLinks\Model\Source;

/**
 * Used in creating options for default destination config value selection
 *
 */
class DefaultDestination extends Source
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $data = [
            ['value' => 'product_page', 'label' => __('Product Page')],
            ['value' => 'category_page', 'label' => __('Category Page')],
            ['value' => 'cms_page_content', 'label' => __('CMS Page Content')],
        ];

        if ($this->landingPage->isLandingPageEnabled()) {
            $data[] = ['value' => 'landingpage', 'label' => __('Landing Page')];
        }

        return $data;
    }
}
