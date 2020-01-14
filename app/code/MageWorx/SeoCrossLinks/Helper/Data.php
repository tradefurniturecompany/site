<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCrossLinks\Helper;

use Magento\Store\Model\ScopeInterface;

/**
 * SEO CrossLinks config data helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * XML config path cross linking enabled
     */
    const XML_PATH_ENABLED = 'mageworx_seo/seocrosslinks/enabled';

    /**
     * XML config path replacement count for product
     */
    const XML_PATH_REPLACEMENT_COUNT_PRODUCT = 'mageworx_seo/seocrosslinks/replacement_count_product';

    /**
     * XML config path replacement count for category
     */
    const XML_PATH_REPLACEMENT_COUNT_CATEGORY = 'mageworx_seo/seocrosslinks/replacement_count_category';

    /**
     * XML config path replacement count for CMS page
     */
    const XML_PATH_REPLACEMENT_COUNT_CMS_PAGE = 'mageworx_seo/seocrosslinks/replacement_count_cms_page';

    /**
     * XML config path replacement count for landing page
     */
    const XML_PATH_REPLACEMENT_COUNT_LANDINGPAGE = 'mageworx_seo/seocrosslinks/replacement_count_landingpage';

    /**
     * XML config path using name of entity for title
     */
    const XML_PATH_USE_NAME_FOR_TITLE = 'mageworx_seo/seocrosslinks/use_name_for_title';

    /**
     * XML config path product attributes for replacing
     */
    const XML_PATH_PRODUCT_ATTRIBUTES = 'mageworx_seo/seocrosslinks/product_attributes';

    /**
     * XML config path default link target
     */
    const XML_PATH_DEFAULT_TARGET = 'mageworx_seo/seocrosslinks/default_target';

    /**
     * XML config path default reference
     */
    const XML_PATH_DEFAULT_REFERENCE = 'mageworx_seo/seocrosslinks/default_reference';

    /**
     * XML config path default replacement count
     */
    const XML_PATH_DEFAULT_REPLACEMENT_COUNT = 'mageworx_seo/seocrosslinks/default_replacement_count';

    /**
     * XML config path default priority
     */
    const XML_PATH_DEFAULT_PRIORITY = 'mageworx_seo/seocrosslinks/default_priority';

    /**
     * XML config path default status
     */
    const XML_PATH_DEFAULT_STATUS = 'mageworx_seo/seocrosslinks/default_status';

    /**
     * XML config path default destination
     */
    const XML_PATH_DEFAULT_DESTINATION = 'mageworx_seo/seocrosslinks/default_destination';

    /**
     * XML config path enabled grid columns
     */
    const XML_PATH_DEFAULT_GRID_COLUMNS = 'mageworx_seo/seocrosslinks/default_grid_columns';

    /**
     * List of default destinations
     *
     * @var array
     */
    protected $destinationDefault = null;

    /**
     * List of enabled grid columns
     *
     * @var array
     */
    protected $gridColumnsDefault = null;

    /**
     * Checks if cross linking is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return (bool) $this->scopeConfig->getValue(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrive max replacement for product page
     *
     * @param int|null $storeId
     * @return int
     */
    public function getReplacemenetCountForProductPage($storeId = null)
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_REPLACEMENT_COUNT_PRODUCT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrive max replacement for category page
     *
     * @param int|null $storeId
     * @return int
     */
    public function getReplacemenetCountForCategoryPage($storeId = null)
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_REPLACEMENT_COUNT_CATEGORY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrive max replacement for CMS page
     *
     * @param int|null $storeId
     * @return int
     */
    public function getReplacemenetCountForCmsPage($storeId = null)
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_REPLACEMENT_COUNT_CMS_PAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrive max replacement for landing page
     *
     * @param int|null $storeId
     * @return int
     */
    public function getReplacemenetCountForLandingPage($storeId = null)
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_REPLACEMENT_COUNT_LANDINGPAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if use product or category name for crosslink title
     */
    public function isUseNameForTitle($storeId = null)
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_USE_NAME_FOR_TITLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrive list of product attributes for replace
     *
     * @param int|null $storeId
     * @return array
     */
    public function getProductAttributesForReplace($storeId = null)
    {
        $productAttributesAsString = $this->scopeConfig->getValue(
            self::XML_PATH_PRODUCT_ATTRIBUTES,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        return array_filter(array_map('trim', explode(',', $productAttributesAsString)));
    }

    /**
     * Retrive default reference
     *
     * @return string
     */
    public function getDefaultReference()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DEFAULT_REFERENCE
        );
    }

    /**
     * Retrive default link target
     *
     * @return int
     */
    public function getDefaultLinkTarget()
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_DEFAULT_TARGET
        );
    }

    /**
     * Retrive default priority
     *
     * @return int
     */
    public function getDefaultPriority()
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_DEFAULT_PRIORITY
        );
    }

    /**
     * Retrive default status
     *
     * @return int
     */
    public function getDefaultStatus()
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_DEFAULT_STATUS
        );
    }

    /**
     * Retrive default count
     *
     * @return int
     */
    public function getDefaultReplacementCount()
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_DEFAULT_REPLACEMENT_COUNT
        );
    }


    /**
     * Retrive default list of destinations
     *
     * @return array
     */
    public function getDefaultDestinationArray()
    {
        if (is_null($this->destinationDefault)) {
            $value = $this->scopeConfig->getValue(
                self::XML_PATH_DEFAULT_DESTINATION
            );

            $arrayRaw = array_map('trim', explode(',', $value));
            $this->destinationDefault = array_filter($arrayRaw);
        }
        return $this->destinationDefault;
    }

    /**
     * Check if destination for product enabled by default
     *
     * @return bool
     */
    public function getDefaultForProductPage()
    {
        return in_array('product_page', $this->getDefaultDestinationArray());
    }

    /**
     * Check if destination for category enabled by default
     *
     * @return bool
     */
    public function getDefaultForCategoryPage()
    {
        return in_array('category_page', $this->getDefaultDestinationArray());
    }

    /**
     * Check if destination for CMS page enabled by default
     *
     * @return bool
     */
    public function getDefaultForCmsPageContent()
    {
        return in_array('cms_page_content', $this->getDefaultDestinationArray());
    }

    /**
     * Check if destination for landing page enabled by default
     *
     * @return bool
     */
    public function getDefaultForLandingPageContent()
    {
        return in_array('landingpage', $this->getDefaultDestinationArray());
    }


    /**
     * @return string
     */
    public function getLinkClass()
    {
        return 'class="crosslink"';
    }

    /**
     * Check if destination for CMS page enabled by default
     *
     * @return bool
     */
    public function getDefaultForNofollowContent()
    {
        return false;
    }

}
