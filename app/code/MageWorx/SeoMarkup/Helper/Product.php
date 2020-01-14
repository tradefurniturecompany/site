<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoMarkup\Helper;

use Magento\Store\Model\ScopeInterface;

/**
 * SEO Markup Product Helper
 */
class Product extends \MageWorx\SeoMarkup\Helper\Data
{
    /**@#+
     * XML config setting paths
     */
    const XML_PATH_PRODUCT_ENABLED                       = 'mageworx_seo/markup/product/rs_enabled';
    const XML_PATH_PRODUCT_OPENGRAPH_ENABLED             = 'mageworx_seo/markup/product/og_enabled';
    const XML_PATH_PRODUCT_TWITTER_ENABLED               = 'mageworx_seo/markup/product/tw_enabled';
    const XML_PATH_CATEGORY_ENABLED                      = 'mageworx_seo/markup/product/category_enabled';
    const XML_PATH_CATEGORY_DEEPEST                      = 'mageworx_seo/markup/product/category_deepest';
    const XML_PATH_BEST_RATING                           = 'mageworx_seo/markup/product/best_rating';
    const XML_PATH_DISABLE_DEFAULT_REVIEW                = 'mageworx_seo/markup/product/disable_default_review';
    const XML_PATH_DESCRIPTION_CODE                      = 'mageworx_seo/markup/product/description_code';
    const XML_PATH_SKU_ENABLED                           = 'mageworx_seo/markup/product/sku_enabled';
    const XML_PATH_SKU_CODE                              = 'mageworx_seo/markup/product/sku_code';
    const XML_PATH_COLOR_ENABLED                         = 'mageworx_seo/markup/product/color_enabled';
    const XML_PATH_COLOR_CODE                            = 'mageworx_seo/markup/product/color_code';
    const XML_PATH_WEIGHT_ENABLED                        = 'mageworx_seo/markup/product/weight_enabled';
    const XML_PATH_WEIGHT_UNIT                           = 'mageworx_seo/markup/product/weight_unit';
    const XML_PATH_MANUFACTURER_ENABLED                  = 'mageworx_seo/markup/product/manufacturer_enabled';
    const XML_PATH_MANUFACTURER_CODE                     = 'mageworx_seo/markup/product/manufacturer_code';
    const XML_PATH_BRAND_ENABLED                         = 'mageworx_seo/markup/product/brand_enabled';
    const XML_PATH_BRAND_CODE                            = 'mageworx_seo/markup/product/brand_code';
    const XML_PATH_MODEL_ENABLED                         = 'mageworx_seo/markup/product/model_enabled';
    const XML_PATH_MODEL_CODE                            = 'mageworx_seo/markup/product/model_code';
    const XML_PATH_GTIN_ENABLED                          = 'mageworx_seo/markup/product/gtin_enabled';
    const XML_PATH_GTIN_CODE                             = 'mageworx_seo/markup/product/gtin_code';
    const XML_PATH_PRODUCT_ID_CODE                       = 'mageworx_seo/markup/product/product_id_code';
    const XML_PATH_CONDITION_ENABLED                     = 'mageworx_seo/markup/product/condition_enabled';
    const XML_PATH_CONDITION_CODE                        = 'mageworx_seo/markup/product/condition_code';
    const XML_PATH_CONDITION_NEW                         = 'mageworx_seo/markup/product/condition_value_new';
    const XML_PATH_CONDITION_REF                         = 'mageworx_seo/markup/product/condition_value_refurbished';
    const XML_PATH_CONDITION_USED                        = 'mageworx_seo/markup/product/condition_value_used';
    const XML_PATH_CONDITION_DAMAGED                     = 'mageworx_seo/markup/product/condition_value_damaged';
    const XML_PATH_CONDITION_DEFAULT                     = 'mageworx_seo/markup/product/condition_value_default';
    const XML_PATH_ENABLED_CUSTOM_PROPERTIES             = 'mageworx_seo/markup/product/custom_prorerty_enabled';
    const XML_PATH_CUSTOM_PROPERTIES                     = 'mageworx_seo/markup/product/custom_prorerties';
    const XML_PATH_PRODUCT_PAGE_GOOGLE_ASSISTANT_ENABLED = 'mageworx_seo/markup/product/ga_enabled';
    const XML_PATH_CSS_SELECTOR                          = 'mageworx_seo/markup/product/ga_css_selector';

    const XML_PATH_SPECIAL_PRICE_FUNCTIONALITY     = 'mageworx_seo/markup/product/special_price_functionality';
    const XML_PATH_PRICE_VALID_UNTIL_DEFAULT_VALUE = 'mageworx_seo/markup/product/price_valid_until_default_value';

    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $helperDirectoryData;

    /**
     *
     * @param \Magento\Directory\Helper\Data $helperDirectoryData
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Directory\Helper\Data $helperDirectoryData,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->helperDirectoryData = $helperDirectoryData;
        parent::__construct($context);
    }

    /**
     * Check if enabled in the rich snippets
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isRsEnabled($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_PRODUCT_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if enabled in the open graph
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isOgEnabled($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_PRODUCT_OPENGRAPH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if enabled in the twitter cards
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isTwEnabled($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_PRODUCT_TWITTER_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if enabled in the google assistant
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isGaEnabled($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_PRODUCT_PAGE_GOOGLE_ASSISTANT_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve the css selector
     *
     * @param int|null $storeId
     * @return int
     */
    public function getGaCssSelectors($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CSS_SELECTOR,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve twitter username
     *
     * @param int|null $storeId
     * @return string
     */
    public function getTwUsername($storeId = null)
    {
        return $this->getCommonTwUsername($storeId);
    }

    /**
     * Check if category enabled
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isCategoryEnabled($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_CATEGORY_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if use deepest category
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isCategoryDeepest($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_CATEGORY_DEEPEST,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if condition enabled
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isConditionEnabled($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_CONDITION_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve description code
     *
     * @param int|null $storeId
     * @return string
     */
    public function getDescriptionCode($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DESCRIPTION_CODE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if SKU enabled
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isSkuEnabled($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_SKU_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve SKU code
     *
     * @param int|null $storeId
     * @return string
     */
    public function getSkuCode($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SKU_CODE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve the best rating
     *
     * @param int|null $storeId
     * @return int
     */
    public function getBestRating($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_BEST_RATING,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if disabled default review markup
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isDisableDefaultReview($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_DISABLE_DEFAULT_REVIEW,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if use Special Price functionality
     *
     * @param int|null $storeId
     * @return mixed
     */
    public function isUseSpecialPriceFunctionality($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SPECIAL_PRICE_FUNCTIONALITY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve priceValidUntil default value
     *
     * @param int|null $storeId
     * @return mixed
     */
    public function getPriceValidUntilDefaultValue($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PRICE_VALID_UNTIL_DEFAULT_VALUE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve productID code
     *
     * @param int|null $storeId
     * @return string
     */
    public function getProductIdCode($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PRODUCT_ID_CODE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve condition code
     *
     * @param int|null $storeId
     * @return string
     */
    public function getConditionCode($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CONDITION_CODE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve condition value for new item
     *
     * @param int|null $storeId
     * @return string
     */
    public function getConditionValueForNew($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CONDITION_NEW,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve condition value for refurbished item
     *
     * @param int|null $storeId
     * @return string
     */
    public function getConditionValueForRefurbished($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CONDITION_NEW,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve condition value for damaged item
     *
     * @param int|null $storeId
     * @return string
     */
    public function getConditionValueForDamaged($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CONDITION_DAMAGED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve condition value for used item
     *
     * @param int|null $storeId
     * @return string
     */
    public function getConditionValueForUsed($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CONDITION_USED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve condition value for used item
     *
     * @param int|null $storeId
     * @return string
     */
    public function getConditionDefaultValue($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CONDITION_DEFAULT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if color enabled
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isColorEnabled($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_COLOR_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve color code
     *
     * @param int|null $storeId
     * @return string
     */
    public function getColorCode($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_COLOR_CODE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if manufacturer enabled
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isManufacturerEnabled($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_MANUFACTURER_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve manufacturer code
     *
     * @param int|null $storeId
     * @return string
     */
    public function getManufacturerCode($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MANUFACTURER_CODE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if brand enabled
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isBrandEnabled($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_BRAND_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve brand code
     *
     * @param int|null $storeId
     * @return string
     */
    public function getBrandCode($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_BRAND_CODE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if model enabled
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isModelEnabled($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_MODEL_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve model code
     *
     * @param int|null $storeId
     * @return string
     */
    public function getModelCode($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MODEL_CODE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if gtin enabled
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isGtinEnabled($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_GTIN_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve gtin code
     *
     * @param int|null $storeId
     * @return string
     */
    public function getGtinCode($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GTIN_CODE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if weight enabled
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isWeightEnabled($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_WEIGHT_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve weight unit
     *
     * @param int|null $storeId
     * @return string
     */
    public function getWeightUnit($storeId = null)
    {
        return $this->helperDirectoryData->getWeightUnit($storeId);
    }

    /**
     * Check if custom properties enabled
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isCustomPropertiesEnabled($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_ENABLED_CUSTOM_PROPERTIES,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     *
     * @param int|null $storeId
     * @return array
     */
    public function getCustomProperties($storeId = null)
    {
        if (!$this->isCustomPropertiesEnabled($storeId)) {
            return [];
        }

        $rawString = $this->scopeConfig->getValue(
            self::XML_PATH_CUSTOM_PROPERTIES,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $string = trim($rawString);
        $pairArray = array_filter(preg_split('/\r?\n/', $string));
        $pairArray = array_filter(array_map('trim', $pairArray));

        $ret = [];
        foreach ($pairArray as $pair) {
            $pair = trim($pair, ',');
            $explode = explode(',', $pair);
            if (is_array($explode) && count($explode) >= 2) {
                $key = trim($explode[0]);
                $val = trim($explode[1]);
                if ($key && $val) {
                    $ret[$key] = $val;
                }
            }
        }
        return $ret;
    }
}
