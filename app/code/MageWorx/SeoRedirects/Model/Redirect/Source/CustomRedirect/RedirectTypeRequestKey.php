<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Model\Redirect\Source\CustomRedirect;

use MageWorx\SeoRedirects\Model\Redirect\CustomRedirect;

class RedirectTypeRequestKey extends \MageWorx\SeoRedirects\Model\Redirect\Source\CustomRedirect
{
    const URL_REQUEST_IDENTIFIER         = 'request_url';
    const PRODUCT_REQUEST_IDENTIFIER     = 'request_product_id';
    const CATEGORY_REQUEST_IDENTIFIER    = 'request_category_id';
    const PAGE_REQUEST_IDENTIFIER        = 'request_page_id';
    const LANDINGPAGE_REQUEST_IDENTIFIER = 'request_landingpage_id';

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $data = [
            [
                'value' => CustomRedirect::REDIRECT_TYPE_CUSTOM,
                'label' => self::URL_REQUEST_IDENTIFIER,
            ],
            [
                'value' => CustomRedirect::REDIRECT_TYPE_PRODUCT,
                'label' => self::PRODUCT_REQUEST_IDENTIFIER,
            ],
            [
                'value' => CustomRedirect::REDIRECT_TYPE_CATEGORY,
                'label' => self::CATEGORY_REQUEST_IDENTIFIER,
            ],
            [
                'value' => CustomRedirect::REDIRECT_TYPE_PAGE,
                'label' => self::PAGE_REQUEST_IDENTIFIER,
            ],
        ];

        if ($this->landingPage->isLandingPageEnabled()) {
            $data[] = [
                'value' => CustomRedirect::REDIRECT_TYPE_LANDINGPAGE,
                'label' => self::LANDINGPAGE_REQUEST_IDENTIFIER,
            ];
        }

        return $data;
    }
}
