<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Converter\LandingPage;

use MageWorx\SeoXTemplates\Model\Converter\LandingPage as ConverterLandingPage;

class UrlKey extends ConverterLandingPage
{
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
