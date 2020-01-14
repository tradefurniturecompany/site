<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Converter;

use MageWorx\SeoXTemplates\Model\Converter;
use Magento\Framework\Pricing\Helper\Data as HelperPrice;
use MageWorx\SeoXTemplates\Helper\Data as HelperData;
use MageWorx\SeoXTemplates\Helper\Converter as HelperConverter;
use Magento\Tax\Helper\Data as HelperTax;
use Magento\Framework\Registry;

abstract class Product extends Converter
{
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $resourceProduct;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     *
     * @var HelperTax
     */
    protected $helperTax;

    /**
     *
     * @var HelperPrice
     */
    protected $helperPrice;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     *
     * @var array
     */
    protected static $_variablesData = [];

    /**
     *
     * @var array
     */
    protected $_dynamicVariables = ['category', 'categories'];

    /**
     * Product constructor.
     *
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param HelperData $helperData
     * @param HelperConverter $helperConverter
     * @param \MageWorx\SeoXTemplates\Model\ResourceModel\Category $resourceCategory
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Catalog\Model\ResourceModel\Product $resourceProduct
     * @param Registry $registry
     * @param HelperPrice $helperPrice
     * @param HelperTax $helperTax
     */
    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        HelperData $helperData,
        HelperConverter $helperConverter,
        \MageWorx\SeoXTemplates\Model\ResourceModel\Category $resourceCategory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Catalog\Model\ResourceModel\Product $resourceProduct,
        Registry $registry,
        HelperPrice $helperPrice,
        HelperTax $helperTax
    ) {
        parent::__construct($storeManager, $helperData, $helperConverter, $resourceCategory, $request);
        $this->priceCurrency   = $priceCurrency;
        $this->registry        = $registry;
        $this->helperPrice     = $helperPrice;
        $this->resourceProduct = $resourceProduct;
        $this->helperTax       = $helperTax;
    }

    /**
     * Returns price converted to current currency rate
     *
     * @param float $price
     * @return float
     */
    public function getCurrencyPrice($price)
    {
        $store = $this->item->getStoreId();
        return $this->pricingHelper->currencyByStore($price, $store, false);
    }

    /**
     * Retrieve converted string by template code
     *
     * @param array $vars
     * @param string $templateCode
     * @return string
     */
    protected function __convert($vars, $templateCode)
    {
        $convertValue = $templateCode;
        $includingTax = $this->displayPriceIncludingTax($this->item->getStoreId());

        foreach ($vars as $key => $params) {
            if (!$this->isDynamically && $this->_issetDynamicAttribute($params['attributes'])) {
                $value = $key;
            } else {
                foreach ($params['attributes'] as $attributeCode) {
                    switch ($attributeCode) {
                        case 'name':
                            $value = $this->_convertName($attributeCode);
                            break;
                        case 'category':
                            $value = $this->_convertCategory();
                            break;
                        case 'categories':
                            $value = $this->_convertCategories();
                            break;
                        case 'store_view_name':
                            $value = $this->_convertStoreViewName();
                            break;
                        case 'store_name':
                            $value = $this->_convertStoreName();
                            break;
                        case 'website_name':
                            $value = $this->_convertWebsiteName();
                            break;
                        case 'price':
                            $value = $this->priceCurrency->format($this->_convertPrice($includingTax), false);
                            break;
                        case 'special_price':
                            $value = $this->priceCurrency->format($this->_convertSpecialPrice($includingTax), false);
                            break;
                        default:
                            $value = $this->_convertAttribute($attributeCode);
                            break;
                    }

                    if ($value) {
                        $prefix = $this->helperConverter->randomizePrefix($params['prefix']);
                        $suffix = $this->helperConverter->randomizeSuffix($params['suffix']);
                        $value = $prefix . $value . $suffix;
                        break;
                    }
                }
            }

            $convertValue = str_replace($key, $value, $convertValue);
        }

        return $this->_render($convertValue);
    }

    /**
     * Retrieve converted string
     *
     * @param string $attribute
     * @return string
     */
    protected function _convertName($attribute)
    {
        return $this->_convertAttribute($attribute);
    }

    /**
     *
     * @return string
     */
    protected function _convertStoreViewName()
    {
        return $this->storeManager->getStore($this->item->getStoreId())->getName();
    }

    /**
     *
     * @return string
     */
    protected function _convertStoreName()
    {
        return $this->storeManager->getStore($this->item->getStoreId())->getGroup()->getName();
    }

    /**
     *
     * @return string
     */
    protected function _convertWebsiteName()
    {
        return $this->storeManager->getStore($this->item->getStoreId())->getWebsite()->getName();
    }

    /**
     *
     * @return string
     */
    protected function _convertCategory()
    {
        $params = $this->_getRequestParams();
        if (empty($params['category'])) {
            return '';
        }

        if (!is_callable([$this->resourceCategory, 'getAttributeRawValue'])) {
            return '';
        } else {
            if (isset(self::$_variablesData['category'])) {
                $value = self::$_variablesData['category'];
            } elseif (isset(self::$_variablesData['categories'])) {
                list($value) = explode(', ', self::$_variablesData['categories']);
            } else {
                $value = $this->_getRawCategoryAttributeValue($params['category'], 'name');
            }
            $value = ($value == 'Root Catalog') ? '' : $value;
            self::$_variablesData['category'] = $value;
            return $value;
        }
        return '';
    }

    /**
     *
     * @return string
     */
    protected function _convertCategories()
    {
        $categoryId = $this->_getCategoryId();

        if (!is_callable([$this->resourceCategory, 'getAttributeRawValue'])) {
            return '';
        } else {
            if (isset(self::$_variablesData['categories'])) {
                return self::$_variablesData['categories'];
            }

            $path      = $this->_getRawCategoryAttributeValue($categoryId, 'path');
            $pathArray = array_reverse(explode('/', $path['path']));
            $separator = $this->helperData->getTitleSeparator($this->item->getStoreId());

            $names = [];
            foreach ($pathArray as $id) {
                if ($categoryId == $id && !empty(self::$_variablesData['category'])) {
                    $category = self::$_variablesData['category'];
                } else {
                    $category = $this->_getRawCategoryAttributeValue($id, 'name');
                }
                if ($category && $category != 'Root Catalog' && $category != 'Default Category') {
                    $names[$id] = $category;
                }
            }
            $value = trim(implode($separator, $names));
            self::$_variablesData['categories'] = $value;

            return $value;
        }
        return '';
    }

    /**
     * @return int|null
     */
    protected function _getCategoryId()
    {
        $params = $this->_getRequestParams();

        if (!empty($params['category'])) {
            return $params['category'];
        }

        // When the category loaded by data from customer session.
        $currentCategory = $this->registry->registry('current_category');
        if ($currentCategory) {
            return $currentCategory->getId();
        }

        return null;
    }

    /**
     * Retrieve converted string
     * @param int $includingTax
     * @return string
     */
    protected function _convertPrice($includingTax)
    {
        return $this->item->getFinalPrice();

        if ($this->item->getTypeId() == 'bundle') {
            $value = $this->_convertPriceForBundle($includingTax);
        } elseif ($this->item->getTypeId() == 'grouped') {
            $value = $this->_convertPriceForGrouped($includingTax);
        } else {
            $value = $this->_convertPriceByDefault($includingTax);
        }
        return $value;
    }

    /**
     * Retrieve converted string
     * @return string
     */
    protected function _convertPriceForBundle()
    {
        return false;
    }

    /**
     * Retrieve converted string
     * @param int $includingTax
     * @return string
     */
    protected function _convertPriceForGrouped($includingTax)
    {
        return false;
    }

    /**
     * Retrieve converted string
     *
     * @param int $includingTax
     * @return string
     */
    protected function _convertSpecialPrice($includingTax)
    {
        return false;
    }

    /**
     * Retrieve converted string
     * @param string $attributeCode
     * @return string
     */
    protected function _convertAttribute($attributeCode)
    {
        $tempValue = '';
        $value     = $this->item->getData($attributeCode);
        if ($_attr     = $this->item->getResource()->getAttribute($attributeCode)) {
            $_attr->setStoreId($this->item->getStoreId());
            if ($_attr->usesSource()) {
                $tempValue = $_attr->setStoreId($this->item->getStoreId())->getSource()->getOptionText($this->item->getData($attributeCode));
            }
        }
        if ($tempValue) {
            $value = $tempValue;
        }
        if (!$value) {
            if ($this->item->getTypeId() == 'configurable') {
                $productAttributeOptions = $this->item->getTypeInstance(true)->getConfigurableAttributesAsArray($this->item);
                $attributeOptions        = [];
                foreach ($productAttributeOptions as $productAttribute) {
                    if ($productAttribute['attribute_code'] == $attributeCode) {
                        foreach ($productAttribute['values'] as $attribute) {
                            $attributeOptions[] = $attribute['store_label'];
                        }
                    }
                }
                if (count($attributeOptions) == 1) {
                    $value = array_shift($attributeOptions);
                }
            } else {
                $value = $this->item->getData($attributeCode);
            }
        }
        return is_array($value) ? implode(', ', $value) : $value;
    }

    /**
     *
     * @param string $converValue
     * @return string
     */
    protected function _render($convertValue)
    {
        return trim($convertValue);
    }

    /**
     * Check if we have display in catalog prices including tax
     *
     * @param int|Store
     * @return bool
     */
    public function displayPriceIncludingTax($store)
    {
        return $this->getPriceDisplayType($store) == \Magento\Tax\Model\Config::DISPLAY_TYPE_INCLUDING_TAX;
    }

    /**
     * Get product price display type
     *  1 - Excluding tax
     *  2 - Including tax
     *  3 - Both
     *
     * @param  int|Store $store
     * @return int
     */
    public function getPriceDisplayType($store)
    {
        return $this->helperTax->getPriceDisplayType($store);
    }

    /**
     * @param array $attributes
     * @return boolean
     */
    protected function _issetDynamicAttribute($attributes)
    {
        return (bool)array_intersect($this->_dynamicVariables, $attributes);
    }

    /**
     * @param string $templateCode
     * @return bool
     */
    protected function stopProccess($templateCode)
    {
        if (!$this->isDynamically) {
            return false;
        }

        $isNotFound = true;

        foreach ($this->_dynamicVariables as $variable) {
            if (strpos($templateCode, '[' . trim($variable) . ']') !== false) {
                $isNotFound = false;
            }

            if (strpos($templateCode, '{' . trim($variable) . '}') !== false) {
                $isNotFound = false;
            }
        }

        return $isNotFound;
    }
}
