<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Model\Redirect\Source\CustomRedirect;

use MageWorx\SeoRedirects\Model\Redirect\CustomRedirect;

class RedirectTypeRewriteFragment extends \MageWorx\SeoRedirects\Model\Redirect\Source\CustomRedirect
{
    const CATEGORY_URL_FRAGMENT    = 'catalog/category/view/id/';
    const PRODUCT_URL_FRAGMENT     = 'catalog/product/view/id/';
    const PAGE_URL_FRAGMENT        = 'cms/page/view/page_id/';
    const LANDINGPAGE_URL_FRAGMENT = 'landingpages/landingpage/view/id/';

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $data = [
            [
                'value' => CustomRedirect::REDIRECT_TYPE_PRODUCT,
                'label' => self::PRODUCT_URL_FRAGMENT,
            ],
            [
                'value' => CustomRedirect::REDIRECT_TYPE_CATEGORY,
                'label' => self::CATEGORY_URL_FRAGMENT,
            ],
            [
                'value' => CustomRedirect::REDIRECT_TYPE_PAGE,
                'label' => self::PAGE_URL_FRAGMENT,
            ],
        ];

        if ($this->landingPage->isLandingPageEnabled()) {
            $data[] = [
                'value' => CustomRedirect::REDIRECT_TYPE_LANDINGPAGE,
                'label' => self::LANDINGPAGE_URL_FRAGMENT,
            ];
        }

        return $data;
    }
}
