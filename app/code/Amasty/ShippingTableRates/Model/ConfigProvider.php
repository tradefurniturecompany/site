<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Model;

use Amasty\Base\Model\ConfigProviderAbstract;
use Amasty\ShippingTableRates\Model\Config\Source\Volumetrictype;
use Zend\Validator\Regex as RegexValidator;

/**
 * Carrier config provider
 */
class ConfigProvider extends ConfigProviderAbstract
{
    /**
     * xpath prefix of module (section)
     * @var string '{section}/{group}/'
     */
    protected $pathPrefix = 'carriers/amstrates/';

    /**#@+
     * xpath field parts
     */
    const XPATH_VOLUMETRIC_WEIGHT = 'volumetric_weight';
    const XPATH_VOLUMETRIC_WEIGHT_ATTRIBUTE = 'volumetric_weight_attribute';
    const XPATH_VOLUMETRIC_ATTRIBUTE = 'volumetric_attribute';
    const XPATH_DIMENSIONS_ATTRIBUTE = 'dimensions_attribute';
    const XPATH_FIRST_SEP_DIMENSION_ATTRIBUTE = 'first_sep_dimension_attribute';
    const XPATH_SECOND_SEP_DIMENSION_ATTRIBUTE = 'second_sep_dimension_attribute';
    const XPATH_THIRD_SEP_DIMENSION_ATTRIBUTE = 'third_sep_dimension_attribute';
    const XPATH_SHIPPING_VACTOR = 'shipping_factor';
    const XPATH_ALLOW_PROMO = 'allow_promo';
    const XPATH_IGNORE_VIRTUAL = 'ignore_virtual';
    const XPATH_AFTER_DISCOUNT = 'after_discount';
    const XPATH_INCLUDING_TAX = 'including_tax';
    const XPATH_CONFIGURABLE_CHILD = 'configurable_child';
    const XPATH_BUNDLE_CHILD = 'bundle_child';
    const XPATH_DONT_SPLIT = 'dont_split';
    /**#@-*/

    const PATTERN_VALID_VOLUME_DIMENSION = '/^((?:\d+?)(?:[.,](?:\d+?)(?=[^\d.,\s]))?)(?:[^\d.,\s])'
    . '((?:\d+?)(?:[.,](?:\d+?)(?=[^\d.,\s]))?)(?:[^\d.,\s])((?:\d+?)(?:[.,](?:\d+?))?)$/';

    /**
     * The method gets value of attribute code
     *
     * @return array
     */
    public function getSelectedWeightAttributeCode()
    {
        $weightAttributeCodes = [];
        $typeVolumetricWeight = $this->getTypeVolumetricWeight();
        switch ($typeVolumetricWeight) {
            case Volumetrictype::VOLUMETRIC_WEIGHT_ATTRIBUTE_TYPE:
                $weightAttributeCodes[] = $this->getVolumetricWeightAttribute();
                break;
            case Volumetrictype::VOLUMETRIC_ATTRIBUTE_TYPE:
                $weightAttributeCodes[] = $this->getVolumetricAttribute();
                break;
            case Volumetrictype::VOLUMETRIC_DIMENSIONS_ATTRIBUTE:
                $weightAttributeCodes[] = $this->getDimensionsAttribute();
                break;
            case Volumetrictype::VOLUMETRIC_SEPARATE_DIMENSION_ATTRIBUTE:
                $weightAttributeCodes[] = $this->getFirstSeparateDimensionAttribute();
                $weightAttributeCodes[] = $this->getSecondSeparateDimensionAttribute();
                $weightAttributeCodes[] = $this->getThirdSeparateDimensionAttribute();
                break;
        }

        return $weightAttributeCodes;
    }

    /**
     * The method  calculates volumetric weight
     *
     * @param mixed $volumeWeight
     *
     * @return float|mixed
     */
    public function calculateVolumetricWeightWithShippingFactor($volumeWeight = 0)
    {
        $volumetricWeight = $volumeWeight;
        $typeVolumetricWeight = $this->getTypeVolumetricWeight();
        $shippingFactor = (int)$this->getShippingFactor();
        if ($typeVolumetricWeight == Volumetrictype::VOLUMETRIC_ATTRIBUTE_TYPE
            || $typeVolumetricWeight == Volumetrictype::VOLUMETRIC_SEPARATE_DIMENSION_ATTRIBUTE
        ) {
            $volumetricWeight = $shippingFactor ? $volumeWeight / $shippingFactor : 0;
        } elseif ($typeVolumetricWeight == Volumetrictype::VOLUMETRIC_DIMENSIONS_ATTRIBUTE) {
            $volumeByDimensions = $this->calculateVolumeByDimensionsAttribtue($volumeWeight);
            $volumetricWeight = $shippingFactor ? $volumeByDimensions / $shippingFactor : 0;
        }

        return (float)$volumetricWeight;
    }

    /**
     * The method calculates volume by dimension
     *
     * @param string $dimensions
     *
     * @return float
     */
    public function calculateVolumeByDimensionsAttribtue($dimensions = '')
    {
        $volume = 0;
        if ($this->isVolumeDimensions($dimensions)) {
            $dimensionNumbers = [];
            preg_match(static::PATTERN_VALID_VOLUME_DIMENSION, $dimensions, $dimensionNumbers);
            array_shift($dimensionNumbers);
            if (!empty($dimensionNumbers)) {
                $volume = 1;
                foreach ($dimensionNumbers as $number) {
                    $number = str_replace(',', '.', $number);
                    $volume *= (float)$number;
                }
            }
        }

        return (float)$volume;
    }

    /**
     * The method checks format of volume dimensions
     *
     * @param string $dimensions
     *
     * @return bool
     */
    private function isVolumeDimensions($dimensions = '')
    {
        $volumeDimensionsValidator = new RegexValidator(static::PATTERN_VALID_VOLUME_DIMENSION);

        return $volumeDimensionsValidator->isValid($dimensions);
    }

    /**
     * The method gets value of the option 'Volumetric weight'
     *
     * @return mixed
     */
    public function getTypeVolumetricWeight()
    {
        return $this->getValue(self::XPATH_VOLUMETRIC_WEIGHT);
    }

    /**
     * The method gets value of 'Volumetric Weight Attribute'
     *
     * @return mixed
     */
    public function getVolumetricWeightAttribute()
    {
        return $this->getValue(self::XPATH_VOLUMETRIC_WEIGHT_ATTRIBUTE);
    }

    /**
     * The method gets value of the option 'Volumetric attribute'
     *
     * @return mixed
     */
    public function getVolumetricAttribute()
    {
        return $this->getValue(self::XPATH_VOLUMETRIC_ATTRIBUTE);
    }

    /**
     * The method gets value of the option 'Dimensions attribute'
     *
     * @return mixed
     */
    public function getDimensionsAttribute()
    {
        return $this->getValue(self::XPATH_DIMENSIONS_ATTRIBUTE);
    }

    /**
     * The method gets value of the option 'Attribute 1'
     *
     * @return mixed
     */
    public function getFirstSeparateDimensionAttribute()
    {
        return $this->getValue(self::XPATH_FIRST_SEP_DIMENSION_ATTRIBUTE);
    }

    /**
     * The method gets value of the option 'Attribute 2'
     *
     * @return mixed
     */
    public function getSecondSeparateDimensionAttribute()
    {
        return $this->getValue(self::XPATH_SECOND_SEP_DIMENSION_ATTRIBUTE);
    }

    /**
     * The method gets value of the option 'Attribute 3'
     *
     * @return mixed
     */
    public function getThirdSeparateDimensionAttribute()
    {
        return $this->getValue(self::XPATH_THIRD_SEP_DIMENSION_ATTRIBUTE);
    }

    /**
     * The method gets value of shipping factor
     *
     * @return mixed
     */
    public function getShippingFactor()
    {
        return $this->getValue(self::XPATH_SHIPPING_VACTOR);
    }

    /**
     * @return bool
     */
    public function isPromoAllowed()
    {
        return $this->isSetFlag(self::XPATH_ALLOW_PROMO);
    }

    /**
     * @return bool
     */
    public function isIgnoreVirtual()
    {
        return $this->isSetFlag(self::XPATH_IGNORE_VIRTUAL);
    }

    /**
     * @return bool
     */
    public function isAfterDiscount()
    {
        return $this->isSetFlag(self::XPATH_AFTER_DISCOUNT);
    }

    /**
     * @return bool
     */
    public function isIncludingTax()
    {
        return $this->isSetFlag(self::XPATH_INCLUDING_TAX);
    }

    /**
     * @return int
     */
    public function getConfigurableSippingType()
    {
        return (int)$this->getValue(self::XPATH_CONFIGURABLE_CHILD);
    }

    /**
     * @return int
     */
    public function getBundleShippingType()
    {
        return (int)$this->getValue(self::XPATH_BUNDLE_CHILD);
    }

    /**
     * @return int
     */
    public function getDontSplit()
    {
        return (int)$this->getValue(self::XPATH_DONT_SPLIT);
    }
}
