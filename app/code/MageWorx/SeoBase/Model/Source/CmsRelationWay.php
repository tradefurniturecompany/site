<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoBase\Model\Source;

class CmsRelationWay extends \MageWorx\SeoBase\Model\Source
{
    public function toOptionArray()
    {
        return [
            [
                'value' => \MageWorx\SeoBase\Helper\Hreflangs::CMS_RELATION_BY_ID,
                'label' => __('By ID')
            ],
            [
                'value' => \MageWorx\SeoBase\Helper\Hreflangs::CMS_RELATION_BY_URLKEY,
                'label' => __('By URL Key')
            ],
            [
                'value' => \MageWorx\SeoBase\Helper\Hreflangs::CMS_RELATION_BY_IDENTIFIER,
                'label' => __('By Hreflang Key')
            ],
        ];
    }
}
