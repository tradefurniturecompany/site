<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Model\Config;

class Product extends \MageWorx\SeoReports\Model\Config\AbstractConfig
{
    /**
     * @var array
     */
    protected $config =
        [
            'reference_id'     => [
                'item_property' => 'entity_id'
            ],
            'store_id'         => [],
            'sku'              => [],
            'meta_title'       =>
                [
                    'meta_title_problems' => [
                        'duplicate' => [
                            'field'       => 'meta_title_duplicate_count',
                            'link'        => 'mageworx_seoreports/product/duplicate',
                            'param_field' => 'prepared_meta_title',
                        ],
                        'length'    => [
                            'field'           => 'meta_title_length',
                            'length_provider' => \MageWorx\SeoReports\Model\LengthDataProvider\MetaTitle::class
                        ],
                        'missing'   => [
                            'field'      => 'prepared_meta_title',
                            'field_type' => 'text',
                        ]
                    ],
                ],
            'meta_description' =>
                [
                    'meta_description_problems' => [
                        'length'  => [
                            'field'           => 'meta_description_length',
                            'length_provider' => \MageWorx\SeoReports\Model\LengthDataProvider\MetaDescription::class
                        ],
                        'missing' => [
                            'field'      => 'meta_description_length',
                            'field_type' => 'length',
                        ]
                    ],
                    'write_to_db'               => false
                ],
            'name'             =>
                [
                    'name_problems' => [
                        'duplicate' => [
                            'field'       => 'name_duplicate_count',
                            'link'        => 'mageworx_seoreports/product/duplicate',
                            'param_field' => 'prepared_name',
                        ],
                        'length'    => [
                            'field'           => 'name_length',
                            'length_provider' => \MageWorx\SeoReports\Model\LengthDataProvider\H1::class
                        ],
                        'missing'   => [
                            'field'      => 'prepared_name',
                            'field_type' => 'text',
                        ]
                    ]
                ],
            'url_path'         => [
                'url_path_problems' => [
                    'length' => [
                        'field'           => 'url_path_length',
                        'length_provider' => \MageWorx\SeoReports\Model\LengthDataProvider\Url::class
                    ],
                ],
                'item_property'     => 'request_path'
            ],
        ];

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
}