<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Configuration Options on Weight calculation
 */
class Volumetrictype implements ArrayInterface
{
    const VOLUMETRIC_WEIGHT_ATTRIBUTE_TYPE = 'volumetric_weight_attribute';
    const VOLUMETRIC_ATTRIBUTE_TYPE = 'volumetric_attribute';
    const VOLUMETRIC_DIMENSIONS_ATTRIBUTE = 'volumetric_dimmensions_attribute';
    const VOLUMETRIC_SEPARATE_DIMENSION_ATTRIBUTE= 'volumetric_separate_dimmension_attribute';

    public function toOptionArray()
    {
        return [
            self::VOLUMETRIC_WEIGHT_ATTRIBUTE_TYPE => __('Volumetric weight attribute'),
            self::VOLUMETRIC_ATTRIBUTE_TYPE => __('Volume attribute'),
            self::VOLUMETRIC_DIMENSIONS_ATTRIBUTE => __('Dimensions attribute'),
            self::VOLUMETRIC_SEPARATE_DIMENSION_ATTRIBUTE => __('Separate dimension attribute')
        ];
    }
}
