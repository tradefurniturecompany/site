<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoMarkup\Helper;

use Magento\Store\Model\ScopeInterface;

/**
 * SEO Markup Page Helper
 */
class Page extends \MageWorx\SeoMarkup\Helper\Data
{
    /**@#+
     * XML config setting paths
     */
    const XML_PATH_PAGE_OPENGRAPH_ENABLED        = 'mageworx_seo/markup/page/og_enabled';
    const XML_PATH_PAGE_TWITTER_ENABLED          = 'mageworx_seo/markup/page/tw_enabled';
    const XML_PATH_PAGE_GOOGLE_ASSISTANT_ENABLED = 'mageworx_seo/markup/page/ga_enabled';
    const XML_PATH_CSS_SELECTOR                  = 'mageworx_seo/markup/page/ga_css_selector';

    /**
     * Check if enabled in the open graph
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isOgEnabled($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_PAGE_OPENGRAPH_ENABLED,
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
            self::XML_PATH_PAGE_TWITTER_ENABLED,
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
            self::XML_PATH_PAGE_GOOGLE_ASSISTANT_ENABLED,
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
}
