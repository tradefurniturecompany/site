<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Converter\LandingPage;

use MageWorx\SeoXTemplates\Model\Converter\LandingPage as ConverterLandingPage;

class Header extends ConverterLandingPage
{
    /**
     *
     * @param string $convertValue
     * @return string
     */
    protected function _render($convertValue)
    {
        $convertValue = parent::_render($convertValue);
        $convertValue = strip_tags($convertValue);

        return trim($convertValue);
    }

    /**
     *
     * @param string $attributeCode
     * @return string
     */
    protected function _convertFilter($attributeCode)
    {
        return '';
    }
}
