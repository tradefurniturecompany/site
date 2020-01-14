<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Model\Redirect\Source;

use MageWorx\SeoRedirects\Model\Redirect\CustomRedirect as CustomRedirectModel;

class RedirectTypeEntityCode extends \MageWorx\SeoRedirects\Model\Redirect\Source\CustomRedirect
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
                'label' => 'custom_url'
            ],
            [
                'value' => CustomRedirectModel::REDIRECT_TYPE_PRODUCT,
                'label' => 'product_id'
            ],
            [
                'value' => CustomRedirectModel::REDIRECT_TYPE_CATEGORY,
                'label' => 'category_id'
            ],
            [
                'value' => CustomRedirectModel::REDIRECT_TYPE_PAGE,
                'label' => 'page_id'
            ],
        ];

        if ($this->landingPage->isLandingPageEnabled()) {
            $data[] = [
                'value' => CustomRedirectModel::REDIRECT_TYPE_LANDINGPAGE,
                'label' => 'landing_page_id',
            ];
        }

        return $data;
    }
}
