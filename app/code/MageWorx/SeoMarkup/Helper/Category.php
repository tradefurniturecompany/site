<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoMarkup\Helper;

use Magento\Store\Model\ScopeInterface;

class Category extends \MageWorx\SeoMarkup\Helper\Data
{
    /**@#+
     * XML config setting paths
     */
    const XML_PATH_CATEGORY_RICHSNIPPET_ENABLED           = 'mageworx_seo/markup/category/rs_enabled';
    const XML_PATH_CATEGORY_OPENGRAPH_ENABLED             = 'mageworx_seo/markup/category/og_enabled';
    const XML_PATH_CATEGORY_USE_OFFERS                    = 'mageworx_seo/markup/category/add_product_offers';
    const XML_PATH_CATEGORY_ROBOTS_RESTRICTION            = 'mageworx_seo/markup/category/robots_restriction';
    const XML_PATH_CATEGORY_PAGE_GOOGLE_ASSISTANT_ENABLED = 'mageworx_seo/markup/category/ga_enabled';
    const XML_PATH_CSS_SELECTOR                           = 'mageworx_seo/markup/category/ga_css_selector';

    /**
     * Check if enabled in the rich snippets
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isRsEnabled($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_CATEGORY_RICHSNIPPET_ENABLED,
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
            self::XML_PATH_CATEGORY_OPENGRAPH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if enabled offer
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isUseOfferForCategoryProducts($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_CATEGORY_USE_OFFERS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if add by robots
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isUseCategoryRobotsRestriction($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_CATEGORY_ROBOTS_RESTRICTION,
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
            self::XML_PATH_CATEGORY_PAGE_GOOGLE_ASSISTANT_ENABLED,
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
}
