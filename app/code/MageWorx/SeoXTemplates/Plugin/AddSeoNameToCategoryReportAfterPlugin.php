<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Plugin;

class AddSeoNameToCategoryReportAfterPlugin
{
    public function afterGetConfig(\MageWorx\SeoReports\Model\Config\AbstractConfig $subject, $result)
    {
        if (empty($result['seo_name'])) {
            $result['seo_name'] =
                [
                    'seo_name_problems' => [
                        'duplicate' => [
                            'field'       => 'seo_name_duplicate_count',
                            'link'        => 'mageworx_seoreports/category/duplicate',
                            'param_field' => 'prepared_seo_name',
                        ],
                        'length'    => [
                            'field'           => 'seo_name_length',
                            'length_provider' => '\MageWorx\SeoReports\Model\LengthDataProvider\MetaDescription'
                        ],
                    ],
                    'item_property'     => 'category_seo_name'
                ];
        }

        return $result;
    }
}