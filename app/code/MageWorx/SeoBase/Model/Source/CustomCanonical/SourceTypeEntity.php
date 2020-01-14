<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\Source\CustomCanonical;

use Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite;

class SourceTypeEntity extends \MageWorx\SeoAll\Model\Source
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
                'value' => Rewrite::ENTITY_TYPE_PRODUCT,
                'label' => __('Product')
            ]
        ];

        return $data;
    }
}
