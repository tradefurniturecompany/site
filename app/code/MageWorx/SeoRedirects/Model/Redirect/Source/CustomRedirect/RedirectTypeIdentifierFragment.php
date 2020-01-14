<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Model\Redirect\Source\CustomRedirect;

use MageWorx\SeoRedirects\Model\Redirect\CustomRedirect;

class RedirectTypeIdentifierFragment extends \MageWorx\SeoRedirects\Model\Redirect\Source\CustomRedirect
{
    const CUSTOM_ENTITY_IDENTIFIER_FRAGMENT      = '';
    const PRODUCT_ENTITY_IDENTIFIER_FRAGMENT     = 'product/';
    const CATEGORY_ENTITY_IDENTIFIER_FRAGMENT    = 'category/';
    const PAGE_ENTITY_IDENTIFIER_FRAGMENT        = '';
    const LANDINGPAGE_ENTITY_IDENTIFIER_FRAGMENT = '';

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
                'label' => self::CUSTOM_ENTITY_IDENTIFIER_FRAGMENT,
            ],
            [
                'value' => CustomRedirect::REDIRECT_TYPE_CATEGORY,
                'label' => self::CATEGORY_ENTITY_IDENTIFIER_FRAGMENT,
            ],
            [
                'value' => CustomRedirect::REDIRECT_TYPE_PRODUCT,
                'label' => self::PRODUCT_ENTITY_IDENTIFIER_FRAGMENT,
            ],
            [
                'value' => CustomRedirect::REDIRECT_TYPE_PAGE,
                'label' => self::PAGE_ENTITY_IDENTIFIER_FRAGMENT,
            ],
        ];

        if ($this->landingPage->isLandingPageEnabled()) {
            $data[] = [
                'value' => CustomRedirect::REDIRECT_TYPE_LANDINGPAGE,
                'label' => self::LANDINGPAGE_ENTITY_IDENTIFIER_FRAGMENT,
            ];
        }

        return $data;
    }
}
