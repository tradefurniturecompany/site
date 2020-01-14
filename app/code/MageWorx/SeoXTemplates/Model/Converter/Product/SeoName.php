<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Converter\Product;

use MageWorx\SeoXTemplates\Model\Converter\Product as ConverterProduct;

class SeoName extends ConverterProduct
{
    protected function _convertStoreViewName()
    {
        return '';
    }

    protected function _convertStoreName()
    {
        return '';
    }

    protected function _convertWebsiteName()
    {
        return '';
    }

    protected function _convertCategory()
    {
        return '';
    }

    protected function _convertCategories()
    {
        return '';
    }

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
}
