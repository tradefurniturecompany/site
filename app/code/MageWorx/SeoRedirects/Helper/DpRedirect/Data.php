<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Helper\DpRedirect;

use Magento\Store\Model\ScopeInterface;

/**
 * SEO Redirects config data helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const DEFAULT_DAY_COUNT_IN_NOT_CLEAN = 30;

    /**
     * XML config path redirects by deleted products enabled
     */
    const XML_PATH_PRODUCT_REDIRECT_ENABLED = 'mageworx_seo/seoredirects/deleted_product/enabled';

    /**
     * XML config path redirect types for redirects by deleted products
     */
    const XML_PATH_PRODUCT_REDIRECT_TYPE = 'mageworx_seo/seoredirects/deleted_product/redirect_type';

    /**
     * XML config path redirect target (own rewrite category / priority category) for redirects by deleted products
     */
    const XML_PATH_PRODUCT_REDIRECT_TARGET = 'mageworx_seo/seoredirects/deleted_product/redirect_target';

    /**
     * XML config path count days for redirects by deleted products
     */
    const XML_PATH_PRODUCT_REDIRECT_STABLE_DAY = 'mageworx_seo/seoredirects/deleted_product/count_stable_day';

    /**
     * Checks if redirects by deleted products is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_PRODUCT_REDIRECT_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve redirects by deleted products type
     *
     * @param int|null $storeId
     * @return int
     */
    public function getRedirectType($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_PRODUCT_REDIRECT_TYPE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Checks if force redirect by category priority is enabled for redirects by deleted products
     *
     * @param int|null $storeId
     * @return int
     */
    public function isForceProductRedirectByPriority($storeId = null)
    {
        $target = (int)$this->scopeConfig->getValue(
            self::XML_PATH_PRODUCT_REDIRECT_TARGET,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $target == \MageWorx\SeoRedirects\Model\Redirect\DpRedirect::TARGET_PRIORITY_CATEGORY;
    }

    /**
     * Retrieve count of previous days during which redirects won't be cleared
     *
     * @return int
     */
    public function getCountStableDay()
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_PRODUCT_REDIRECT_STABLE_DAY,
            ScopeInterface::SCOPE_WEBSITES
        );
    }
}
