<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoUrls\Helper;

use Magento\Catalog\Model\Layer\FilterableAttributeListInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use MageWorx\SeoAll\Helper\Layer as SeoAllHelperLayer;

/**
 * SEO URLs layer helper
 */
class Layer extends AbstractHelper
{
    /**
     * Core data
     *
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filter;

    /**
     * @var FilterableAttributeListInterface
     */
    protected $filterableAttributes;

    /**
     * @var SeoAllHelperLayer
     */
    protected $helperLayer;

    /**
     * @var array|null
     */
    protected $loadedAttributesData = null;

    /**
     * Layer constructor.
     * @param FilterableAttributeListInterface $filterableAttributes
     */
    public function __construct(
        \Magento\Framework\Filter\FilterManager $filter,
        \Magento\Catalog\Model\Layer\FilterableAttributeListInterface $filterableAttributes,
        SeoAllHelperLayer $helperLayer
    ) {
        $this->filter = $filter;
        $this->filterableAttributes = $filterableAttributes;
        $this->helperLayer = $helperLayer;
    }

    /**
     * @param array $layerParams
     * @return array
     */
    public function parseLayeredParams($layerParams)
    {
        $paramsFilteredByAttribute = [];

        foreach ($layerParams as $code => $value) {
            if ($this->isHiddenAttribute($code)) {
                if ($data = $this->getHiddenOptionDataByAttribute($value)) {
                    $paramsFilteredByAttribute[$data['attribute_code']] = $data['option_id'];
                }
            } else {
                if ($data = $this->getNotHiddenOptionDataByAttribute($code, $value)) {
                    $paramsFilteredByAttribute[$data['attribute_code']] = $data['option_id'];
                }
            }
        }
        return $paramsFilteredByAttribute;
    }

    /**
     * @param $value
     * @return string|false
     */
    protected function getHiddenOptionDataByAttribute($value)
    {
        $attr = $this->getFilterableAttributes();

        foreach ($attr as $attrCode => $attrData) {
            foreach ($attrData['options'] as $optionId => $optionLabel) {
                if ($value == $this->formatUrlKey($optionLabel)) {
                    return ['option_id' => $optionId, 'attribute_code' => $attrCode];
                }
            }
        }

        return false;
    }

    /**
     * @param string $code
     * @param string $value
     * @return bool|array
     */
    protected function getNotHiddenOptionDataByAttribute($code, $value)
    {
        if ($code == 'price') {
            if (strpos($value, '-') !== false) {
                $multipliers = explode('-', $value);
                $priceFrom   = floatval($multipliers[0]);
                $priceTo     = $multipliers[1] ? floatval($multipliers[1]) : '';
                $value       = $priceFrom . '-' . $priceTo;

                return ['option_id' => $value, 'attribute_code' => $code];
            }
            return false;
        }

        $attr = $this->getFilterableAttributes();
        $code = str_replace('-', '_', $code); // attrCode is only = [a-z0-9_]

        if (empty($attr[$code])) {
            return false;
        }

        $attributeSortableValues = $this->getPreparedOptions($attr[$code]);

        uasort($attributeSortableValues, function($l, $r) {return strlen($r) > strlen($l);});

        $options = [];
        $modifiedValue = $value;

        foreach ($attributeSortableValues as $optionId => $optionLabel) {

            if (!$optionLabel || !$modifiedValue) {
                continue;
            }
            $pos = mb_strpos($modifiedValue, $optionLabel);

            if ($pos !== false && mb_strpos($value, $optionLabel) !== false) {

                $options[] = $optionId;
                $modifiedValue = substr_replace($modifiedValue, '', $pos, mb_strlen($optionLabel));

                if ($modifiedValue) {
                    if ($pos === 0) {
                        $modifiedValue = substr_replace($modifiedValue, '', 0, 1);
                    } else {
                        $modifiedValue = substr_replace($modifiedValue, '', $pos - 1, 1);
                    }
                }
            }
        }

        if (!$options) {
            return false;
        }
        $result = [
            'attribute_code' => $code,
            'option_id' => implode($this->helperLayer->getMultipleValueSeparator(), $options)
        ];
        return $result;
    }

    /**
     * Retrieve formatted option array sortable by delimiter count
     *
     * @param array $attributeData
     * @return array
     */
    protected function getPreparedOptions($attributeData)
    {
        $options = $attributeData['options'];
        $options = array_map([$this, 'formatUrlKey'], $options);
        uasort($options, [$this, 'compareBySeparaterCount']);
        return $options;
    }

    /**
     * @param string $a
     * @param string $b
     * @return int
     */
    protected function compareBySeparaterCount($a, $b)
    {
        $aCount = substr_count($a, $this->helperLayer->getMultipleValueSeparator());
        $bCount = substr_count($b, $this->helperLayer->getMultipleValueSeparator());

        if ($aCount == $bCount) {
            return 0;
        }
        return ($aCount > $bCount) ? -1 : 1;
    }

    /**
     * @param $param
     * @return bool
     */
    protected function isHiddenAttribute($param)
    {
        return (is_numeric($param));
    }

    /**
     * @return bool
     */
    protected function isHiddenPriceAttribute()
    {
        return false;
    }

    /**
     * @return array|null
     */
    public function getFilterableAttributes()
    {
        if ($this->loadedAttributesData !== null) {
            return $this->loadedAttributesData;
        }

        $attributesData = [];
        $attributeCollection = $this->filterableAttributes->getList();

        foreach ($attributeCollection as $attribute) {
            $attributeCode = $attribute->getAttributeCode();

            $attributesData[$attributeCode]['type'] = $attribute->getBackendType();
            $options = $attribute->getSource()->getAllOptions();

            foreach ($options as $option) {
                //$attributesData[$attributeCode]['options'][$this->formatUrlKey($option['label'])] = $option['label'];
                $attributesData[$attributeCode]['options'][$option['value']] = $option['label'];
                //$attributesData[$attributeCode]['options'][$option['value']] = $this->getOptionLabel($option['label'], $option['value']);
                $attributesData[$attributeCode]['frontend_label'] = $attribute->getFrontendLabel();
            }
        }

        $this->loadedAttributesData = $attributesData;
        return $this->loadedAttributesData;
    }
    /*
    * @param string $optionLabel
    * @param string $optionValue
    * return string
    */
    protected function getOptionLabel($optionLabel, $optionValue)
    {
        $translatedLabel = $this->filter->translitUrl($optionLabel);
        if (!$translatedLabel) {
            $translatedLabel = $optionValue;
        } elseif (in_array($translatedLabel, $this->optionLabelList)) {
            return $this->getOptionLabel($translatedLabel . '-1', $optionValue);
        }
        $this->optionLabelList[] = $translatedLabel;

        return $translatedLabel;
    }

    /**
     * Format URL key from name or defined key
     *
     * @param string $str
     * @return string
     */
    public function formatUrlKey($str)
    {
        $urlKey = $this->filter->translitUrl($str);
        if (!$urlKey) {
            $urlKey = urlencode($str);
        }
        return $urlKey;
    }
}
