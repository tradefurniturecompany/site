<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

/**
 * XML Sitemap data helper
 *
 */
namespace MageWorx\XmlSitemap\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\ObjectManagerInterface;

class Data extends \Magento\Sitemap\Helper\Data
{
    /**
     * XML config path show homepage optimization enabled
     */
    const XML_PATH_HOMEPAGE_OPTIMIZE           = 'mageworx_seo/xml_sitemap/homepage_optimize';

    /**
     * XML config path links enabled
     */
    const XML_PATH_SHOW_LINKS                  = 'mageworx_seo/xml_sitemap/enable_additional_links';

    /**
     * XML config path links
     */
    const XML_PATH_ADDITIONAL_LINKS            = 'mageworx_seo/xml_sitemap/additional_links';

    /**
     * XML config setting change frequency
     */
    const XML_PATH_ADDITIONAL_LINK_CHANGEFREQ  = 'mageworx_seo/xml_sitemap/additional_links_changefreq';

    /**
     * XML config setting change priority
     */
    const XML_PATH_ADDITIONAL_LINK_PRIORITY    = 'mageworx_seo/xml_sitemap/additional_links_priority';

    /**
     * XML config path trailing slash for home page URL
     */
    const XML_PATH_TRAILING_SLASH_FOR_HOME     = 'mageworx_seo/common_sitemap/trailing_slash_home_page';

    /**
     * XML config path trailing slash for URL
     */
    const XML_PATH_TRAILING_SLASH              = 'mageworx_seo/common_sitemap/trailing_slash';

    /**
     * XML config path exclude out of stock products
     */
    const XML_PATH_EXCLUDE_OUT_OF_STOCK_PRODUCTS = 'mageworx_seo/xml_sitemap/exclude_out_of_stock_products';

    /**
     * XML config path add Alternate Hreflang URLs
     */
    const XML_PATH_ADD_HREFLANGS                = 'mageworx_seo/xml_sitemap/add_hreflangs';

    /**
     * XML config path Enable Validate Urls
     */
    const XML_PATH_ENABLE_VALIDATE_URLS         = 'mageworx_seo/xml_sitemap/enable_validate_urls';

    /**
     * XML config path Exclude Noindex Pages
     */
    const XML_PATH_EXCLUDE_NOINDEX              = 'mageworx_seo/xml_sitemap/exclude_noindex';

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $modelDate;

    /**
     * @var \MageWorx\SeoAll\Helper\Config
     */
    protected $helperConfig;


    protected $storeId = null;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $modelDate
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $modelDate,
        \MageWorx\SeoAll\Helper\Config $helperConfig,
        ObjectManagerInterface $objectManager
    ) {
        parent::__construct($context);
        $this->objectManager = $objectManager;
        $this->modelDate = $modelDate;
        $this->helperConfig = $helperConfig;
    }

    public function getProductCanonicalUrlType($storeId = null)
    {
        return $this->helperConfig->getProductCanonicalUrlType($storeId);
    }

    /**
     * Check if optimization home page URL and priority
     *
     * @return bool
     */
    public function isOptimizeHomePage()
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_HOMEPAGE_OPTIMIZE,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Check if show additional links
     *
     * @return bool
     */
    public function isShowLinks()
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_SHOW_LINKS,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Retrieve additional links
     *
     * @return array
     */
    public function getAdditionalLinks()
    {
        $linksString = $this->scopeConfig->getValue(
            self::XML_PATH_ADDITIONAL_LINKS,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );

        $linksArray = array_filter(preg_split('/\r?\n/', $linksString));
        $linksArray = array_map('trim', $linksArray);
        return array_filter($linksArray);
    }

    /**
     * Retrieve additional links as prepared array of \Magento\Framework\Object objects
     *
     * @return array
     */
    public function getAdditionalLinkCollection()
    {
        $links = [];
        foreach ($this->getAdditionalLinks($this->storeId) as $link) {
            $object = new \Magento\Framework\DataObject();
            $object->setUrl($link);
            $object->setUpdatedAt($this->modelDate->gmtDate('Y-m-d'));
            $links[] = $object;
        }
        return $links;
    }

    /**
     * @return string
     */
    public function getCurrentDate()
    {
        return $this->modelDate->gmtDate('Y-m-d');
    }

    /**
     * Retrieve home page identifier
     *
     * @return string
     */
    public function getHomeIdentifier()
    {
        return $this->scopeConfig->getValue(
            \Magento\Cms\Helper\Page::XML_PATH_HOME_PAGE,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Get additional link change frequency
     *
     * @return string
     */
    public function getAdditionalLinkChangefreq()
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_ADDITIONAL_LINK_CHANGEFREQ,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Get additional link priority
     *
     * @param int $storeId
     * @return string
     */
    public function getAdditionalLinkPriority()
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_ADDITIONAL_LINK_PRIORITY,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Checks if add or crop trailing slash for URL
     *
     * @return int
     */
    public function getTrailingSlash()
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_TRAILING_SLASH,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Checks if add or crop trailing slash for home page URL
     *
     * @return int
     */
    public function getTrailingSlashForHomePage()
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_TRAILING_SLASH_FOR_HOME,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Check if is Exclude Out Of Stock Products
     *
     * @return bool
     */
    public function isExcludeOutOfStockProducts()
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_EXCLUDE_OUT_OF_STOCK_PRODUCTS,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Check if need to add Alternate Hreflang URLs
     *
     * @return bool
     */
    public function isAlternateUrlsEnabled()
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_ADD_HREFLANGS,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Check if Enable Validate Urls
     *
     * @return bool
     */
    public function isEnableValidateUrls()
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_ENABLE_VALIDATE_URLS,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Check if Exclude Noindex Pages
     *
     * @return bool
     */
    public function isExcludeNoindex()
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_EXCLUDE_NOINDEX,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * @param $storeId
     */
    public function init($storeId)
    {
        $this->storeId = $storeId;
    }

    /**
     * Get product image include policy
     *
     * @return string
     */
    public function isProductImages()
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_PRODUCT_IMAGES_INCLUDE,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Get maximum file size in bytes
     *
     * @return int
     */
    public function getSplitSize()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MAX_FILE_SIZE,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * @return mixed
     */
    public function isUseCssForXmlSitemap()
    {
        //@todo add setting load css
        return false;
    }

    /**
     * Get maximum URLs number
     *
     * @return int
     */
    public function getMaxLinks()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MAX_LINES,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * @param $url
     * @param bool $isHome
     * @return string
     */
    public function trailingSlash($url, $isHome = false)
    {
        if ($isHome) {
            $trailingSlash = $this->getTrailingSlashForHomePage();
        } else {
            $trailingSlash = $this->getTrailingSlash();
        }

        if ($trailingSlash == 1) {
            $url        = rtrim($url);
            $extensions = ['rss', 'html', 'htm', 'xml', 'php'];
            if (substr($url, -1) != '/' && !in_array(substr(strrchr($url, '.'), 1), $extensions)) {
                $url.= '/';
            }
        } elseif ($trailingSlash == 0) {
            $url = rtrim(rtrim($url), '/');
        }

        return $url;
    }

    /**
     * @param $type
     * @return bool
     */
    public function getHreflangFinalCodes($type)
    {
        return false;
    }
}
