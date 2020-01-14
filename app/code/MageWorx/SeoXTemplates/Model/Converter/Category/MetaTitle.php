<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Converter\Category;

use MageWorx\SeoXTemplates\Model\Converter\Category as ConverterCategory;

class MetaTitle extends ConverterCategory
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

        if ($this->helperData->isCropMetaTitle($this->item->getStoreId())) {
            $length       = $this->helperData->getMaxLengthMetaTitle($this->item->getStoreId());
            $convertValue = mb_substr($convertValue, 0, $length);
        }
        return trim($convertValue);
    }
}
