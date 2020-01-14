<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCrossLinks\Model\Crosslink\Source\Crosslink;

use MageWorx\SeoCrossLinks\Model\Crosslink;
use MageWorx\SeoAll\Helper\LandingPage;

class CrosslinkTypeKey extends \MageWorx\SeoAll\Model\Source
{
    const URL_REQUEST_IDENTIFIER         = 'ref_static_url';
    const PRODUCT_REQUEST_IDENTIFIER     = 'ref_product_sku';
    const CATEGORY_REQUEST_IDENTIFIER    = 'ref_category_id';
    const LANDINGPAGE_REQUEST_IDENTIFIER = 'ref_landingpage_id';

    /**
     * @var LandingPage
     */
    protected $landingPage;

    /**
     * DefaultDestination constructor.
     *
     * @param LandingPage $landingPage
     */
    public function __construct(LandingPage $landingPage)
    {
        $this->landingPage = $landingPage;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $data = [
            [
                'value' => Crosslink::REFERENCE_TO_STATIC_URL,
                'label' => self::URL_REQUEST_IDENTIFIER
            ],
            [
                'value' => Crosslink::REFERENCE_TO_PRODUCT_BY_SKU,
                'label' => self::PRODUCT_REQUEST_IDENTIFIER
            ],
            [
                'value' => Crosslink::REFERENCE_TO_CATEGORY_BY_ID,
                'label' => self::CATEGORY_REQUEST_IDENTIFIER
            ],
        ];

        if ($this->landingPage->isLandingPageEnabled()) {
            $data[] = [
                'value' => Crosslink::REFERENCE_TO_LANDINGPAGE_BY_ID,
                'label' => self::LANDINGPAGE_REQUEST_IDENTIFIER
            ];
        }

        return $data;
    }
}
