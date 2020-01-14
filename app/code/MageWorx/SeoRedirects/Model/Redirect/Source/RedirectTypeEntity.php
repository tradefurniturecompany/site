<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Model\Redirect\Source;

use MageWorx\SeoRedirects\Model\Redirect\CustomRedirect as CustomRedirectModel;

class RedirectTypeEntity extends \MageWorx\SeoRedirects\Model\Redirect\Source\CustomRedirect
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $data = [
            [
                'value' => CustomRedirectModel::REDIRECT_TYPE_CUSTOM,
                'label' => __('Custom URL')
            ],
            [
                'value' => CustomRedirectModel::REDIRECT_TYPE_PRODUCT,
                'label' => __('Product')
            ],
            [
                'value' => CustomRedirectModel::REDIRECT_TYPE_CATEGORY,
                'label' => __('Category')
            ],
            [
                'value' => CustomRedirectModel::REDIRECT_TYPE_PAGE,
                'label' => __('Page')
            ],
        ];

        if ($this->landingPage->isLandingPageEnabled()) {
            $data[] = [
                'value' => CustomRedirectModel::REDIRECT_TYPE_LANDINGPAGE,
                'label' => __('Landing Page'),
            ];
        }

        return $data;
    }
}
