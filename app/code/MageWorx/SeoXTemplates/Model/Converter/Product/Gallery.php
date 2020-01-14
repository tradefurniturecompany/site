<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Converter\Product;

use MageWorx\SeoXTemplates\Model\Converter\Product as ConverterProduct;

class Gallery extends ConverterProduct
{
    /**
     * @param string $attributeCode
     * @return mixed|string
     */
    protected function _convertAttribute($attributeCode)
    {
        if ($attributeCode === 'image_position') {
            return $this->item->getData('current_image_position');
        }

        return parent::_convertAttribute($attributeCode);
    }
}
