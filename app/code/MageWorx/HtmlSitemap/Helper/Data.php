<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\HtmlSitemap\Helper;

use Magento\Store\Model\ScopeInterface;

/**
 * HTML Sitemap config data helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * XML config path show stores enabled
     */
    const XML_PATH_SHOW_STORE              = 'mageworx_seo/html_sitemap/show_stores';

    /**
     * XML config path show categories enabled
     */
    const XML_PATH_SHOW_CATEGORIES         = 'mageworx_seo/html_sitemap/show_categories';

    /**
     * XML config path category max depth
     */
    const XML_PATH_CATEGORY_MAX_DEPTH      = 'mageworx_seo/html_sitemap/category_max_depth';

    /**
     * XML config path show products enabled
     */
    const XML_PATH_SHOW_PRODUCTS           = 'mageworx_seo/html_sitemap/show_products';

    /**
     * XML config path category display mode enabled
     */
    const XML_PATH_USE_CAT_DISPLAY_MODE    = 'mageworx_seo/html_sitemap/use_cat_display_mode';

    /**
     * XML config path product URL length
     */
    const XML_PATH_PRODUCT_URL_LENGTH      = 'mageworx_seo/html_sitemap/product_url_length';

    /**
     * XML config path product URL length category-product sort order
     */
    const XML_PATH_CAT_PROD_SORT_ORDER     = 'mageworx_seo/html_sitemap/cat_prod_sort_order';

    /**
     * XML config path CMS pages enabled
     */
    const XML_PATH_SHOW_CMS_PAGES          = 'mageworx_seo/html_sitemap/show_cms_pages';

    /**
     * XML config path links enabled
     */
    const XML_PATH_SHOW_LINKS              = 'mageworx_seo/html_sitemap/show_links';

    /**
     * XML config path links
     */
    const XML_PATH_ADDITIONAL_LINKS        = 'mageworx_seo/html_sitemap/additional_links';

    /**
     * XML config path custom links enabled
     */
    const XML_PATH_SHOW_CUSTOM_LINKS       = 'mageworx_seo/html_sitemap/show_custom_links';

    /**
     * XML config path sitemap title
     */
    const XML_PATH_TITLE                   = 'mageworx_seo/html_sitemap/title';

    /**
     * XML config path sitemap meta description
     */
    const XML_PATH_META_DESCRIPTION        = 'mageworx_seo/html_sitemap/meta_description';

    /**
     * XML config path sitemap meta keywords
     */
    const XML_PATH_META_KEYWORDS           = 'mageworx_seo/html_sitemap/meta_keywords';

    /**
     * XML config path trailing slash for home page URL
     */
    const XML_PATH_TRAILING_SLASH_FOR_HOME = 'mageworx_seo/common_sitemap/trailing_slash_home_page';

    /**
     * XML config path trailing slash for URL
     */
    const XML_PATH_TRAILING_SLASH          = 'mageworx_seo/common_sitemap/trailing_slash';


    /**
     * Checks if stores are enabled
     *
     * @param int $storeId
     * @return bool
     */
    public function isShowStores($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_SHOW_STORE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Checks if categories are enabled
     *
     * @param int $storeId
     * @return bool
     */
    public function isShowCategories($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_SHOW_CATEGORIES,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve max category depth
     *
     * @param int $storeId
     * @return int
     */
    public function getCategoryMaxDepth($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_CATEGORY_MAX_DEPTH,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Checks if categories are enabled
     *
     * @param int $storeId
     * @return bool
     */
    public function isShowProducts($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_SHOW_PRODUCTS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve the usage of category display mode
     *
     * @param int $storeId
     * @return bool
     */
    public function isUseCategoryDisplayMode($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_USE_CAT_DISPLAY_MODE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve product URL length (usage categories in product URL)
     *
     * @param int $storeId
     * @return int
     */
    public function getProductUrlLength($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_PRODUCT_URL_LENGTH,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve category-product sort order
     *
     * @param int $storeId
     * @return int
     */
    public function getCatProdSortOrder($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_CAT_PROD_SORT_ORDER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Checks if CMS pages are enabled
     *
     * @param int $storeId
     * @return bool
     */
    public function isShowCmsPages($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_SHOW_CMS_PAGES,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Checks if links are enabled
     *
     * @param int $storeId
     * @return bool
     */
    public function isShowLinks($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_SHOW_LINKS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve sitemap page title
     *
     * @param int $storeId
     * @return string
     */
    public function getTitle($storeId = null)
    {
        return htmlspecialchars(strip_tags($this->scopeConfig->getValue(
            self::XML_PATH_TITLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        )));
    }

    /**
     * Retrieve sitemap page meta description
     *
     * @param int $storeId
     * @return string
     */
    public function getMetaDescription($storeId = null)
    {
        return htmlspecialchars(strip_tags($this->scopeConfig->getValue(
            self::XML_PATH_META_DESCRIPTION,
            ScopeInterface::SCOPE_STORE,
            $storeId
        )));
    }

    /**
     * Retrieve sitemap page meta keywords
     *
     * @param int $storeId
     * @return string
     */
    public function getMetaKeywords($storeId = null)
    {
        return htmlspecialchars(strip_tags($this->scopeConfig->getValue(
            self::XML_PATH_META_KEYWORDS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        )));
    }

    /**
     * Retrieve additional links
     *
     * @param int $storeId
     * @return array
     */
    public function getAdditionalLinks($storeId = null)
    {
        $linksString = $this->scopeConfig->getValue(
            self::XML_PATH_ADDITIONAL_LINKS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $linksArrayRaw = array_filter(preg_split('/\r?\n/', $linksString));
        $linksArray = array_map('trim', $linksArrayRaw);
        return array_filter($linksArray);
    }

    /**
     * Checks if custom links are enabled
     *
     * @param int $storeId
     * @return bool
     */
    public function isShowCustomLinks($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_SHOW_CUSTOM_LINKS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve home page identifies
     *
     * @param int $storeId
     * @return string
     */
    public function getHomeIdentifier($storeId = null)
    {
        return $this->scopeConfig->getValue(
            \Magento\Cms\Helper\Page::XML_PATH_HOME_PAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Checks if add or crop trailing slash for URL
     *
     * @param int $storeId
     * @return int
     */
    public function getTrailingSlash($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_TRAILING_SLASH,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Checks if add or crop trailing slash for home page URL
     *
     * @param int $storeId
     * @return int
     */
    public function getTrailingSlashForHomePage($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_TRAILING_SLASH_FOR_HOME,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
