<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBreadcrumbs\Helper;

use MageWorx\SeoBreadcrumbs\Model\Source\Type;
use Magento\Store\Model\ScopeInterface;

/**
 * SEO Breadcrumbs config data helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const BREADCRUMBS_PRIORITY_CODE = 'breadcrumbs_priority';

    /**
     * XML config path SEO Breadcrumbs enabled
     */
    const XML_PATH_BREADCRUMBS_ENABLED                     = 'mageworx_seo/breadcrumbs/enabled';

    /**
     * XML config path SEO Breadcrumbs type
     */
    const XML_PATH_BREADCRUMBS_TYPE                        = 'mageworx_seo/breadcrumbs/type';

    /**
     * XML config path use category breadcrumbs priority
     */
    const XML_PATH_BREADCRUMBS_BY_CATEGORY                 = 'mageworx_seo/breadcrumbs/by_category_priority';

    /**
     * Check if SEO Breadcrumbs enabled
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isSeoBreadcrumbsEnabled($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_BREADCRUMBS_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve SEO Breadcrumbs Path
     *
     * @param int|null $storeId
     * @return int
     */
    public function getSeoBreadcrumbsType($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_BREADCRUMBS_TYPE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if SEO Breadcrumbs by category breadcrumbs priority enabled
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isUseCategoryBreadcrumbsPriority($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_BREADCRUMBS_BY_CATEGORY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if long breadcrumbs mode
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isLongMode($storeId = null)
    {
        return $this->getSeoBreadcrumbsType($storeId) == Type::BREADCRUMBS_TYPE_LONGEST;
    }

    /**
     * Check if short breadcrumbs mode
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isShortMode($storeId = null)
    {
        return $this->getSeoBreadcrumbsType($storeId) == Type::BREADCRUMBS_TYPE_SHORTEST;
    }

    /**
     * Check if default breadcrumbs mode
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isDefaultMode($storeId = null)
    {
        return $this->getSeoBreadcrumbsType($storeId) == Type::BREADCRUMBS_TYPE_DEFAULT;
    }
}
