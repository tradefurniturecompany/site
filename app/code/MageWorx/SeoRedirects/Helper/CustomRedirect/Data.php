<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Helper\CustomRedirect;

use MageWorx\SeoRedirects\Model\Redirect\Source\CustomRedirect\RedirectTypeRewriteFragment as RedirectTypeRewriteFragmentSource;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * SEO Redirects config data helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const DEFAULT_DAY_COUNT_IN_NOT_CLEAN = 30;

    /**
     * XML config path custom redirects enabled
     */
    const XML_PATH_CUSTOM_REDIRECT_ENABLED = 'mageworx_seo/seoredirects/custom/enabled';

    /**
     * XML config path custom redirect keep URLs for deleted entities
     */
    const XML_PATH_CUSTOM_REDIRECT_KEEP_DELETED_URLS = 'mageworx_seo/seoredirects/custom/keep_for_deleted_entities';

    /**
     * @var RedirectTypeRewriteFragmentSource
     */
    protected $redirectTypeRewriteFragmentSource;

    /**
     * Data constructor.
     *
     * @param RedirectTypeRewriteFragmentSource $redirectTypeRewriteFragmentSource
     * @param Context $context
     */
    public function __construct(
        RedirectTypeRewriteFragmentSource $redirectTypeRewriteFragmentSource,
        Context $context
    ) {
        $this->redirectTypeRewriteFragmentSource = $redirectTypeRewriteFragmentSource;
        parent::__construct($context);
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_CUSTOM_REDIRECT_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int|null $storeId
     * @return int
     */
    public function isKeepUrlsForDeletedEntities($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_CUSTOM_REDIRECT_KEEP_DELETED_URLS,
            ScopeInterface::SCOPE_WEBSITES,
            $storeId
        );
    }

    /**
     * @param string $url
     * @return string
     */
    public function addTrailingSlash($url)
    {
        $url        = rtrim($url);
        $extensions = ['rss', 'html', 'htm', 'xml', 'php'];
        if (substr($url, -1) != '/' && !in_array(substr(strrchr($url, '.'), 1), $extensions)) {
            $url .= '/';
        }

        foreach ($this->redirectTypeRewriteFragmentSource->toArray() as $fragment) {
            if (strpos($url, $fragment) !== false) {
                $url = trim($url, '/');
                break;
            }
        }

        return $url;
    }
}
