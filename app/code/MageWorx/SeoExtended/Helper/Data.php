<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Helper;

use Magento\Store\Model\ScopeInterface;
use MageWorx\SeoExtended\Model\Source\AddPageNum;

/**
 * SEO Extended config data helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**#@+
     * XML config paths
     */
    const XML_PATH_USE_SEO_FILTERS          = 'mageworx_seo/extended/seo_filters/use_seo_for_category_filters';
    const XML_PATH_USE_SEO_ON_SINGLE_FILTER = 'mageworx_seo/extended/seo_filters/use_on_single_filter';

    const XML_PATH_ADD_PAGER_NUM_IN_TITLE                = 'mageworx_seo/extended/meta/pager_in_title';
    const XML_PATH_ADD_PAGER_NUM_IN_DESCRIPTION          = 'mageworx_seo/extended/meta/pager_in_description';
    const XML_PATH_ADD_PAGER_NUM_IN_KEYWORDS             = 'mageworx_seo/extended/meta/pager_in_keywords';
    const XML_PATH_CUT_MAGENTO_PREFIX_SUFFIX             = 'mageworx_seo/extended/meta/cut_title_prefix_suffix';
    const XML_PATH_CUT_MAGENTO_PREFIX_SUFFIX_PAGES       = 'mageworx_seo/extended/meta/cut_prefix_suffix_pages';
    const XML_PATH_USE_LAYERED_FILTERS_IN_TITLE          = 'mageworx_seo/extended/meta/layered_filters_in_title';
    const XML_PATH_USE_LAYERED_FILTERS_IN_DESCRIPTION    = 'mageworx_seo/extended/meta/layered_filters_in_description';
    const XML_PATH_USE_LAYERED_FILTERS_IN_KEYWORDS       = 'mageworx_seo/extended/meta/layered_filters_in_keywords';
    const XML_PATH_ADD_PAGER_NUM_IN_LP_TITLE             = 'mageworx_seo/extended/meta/pager_in_lp_title';
    const XML_PATH_ADD_PAGER_NUM_IN_LP_DESCRIPTION       = 'mageworx_seo/extended/meta/pager_in_lp_description';
    const XML_PATH_ADD_PAGER_NUM_IN_LP_KEYWORDS          = 'mageworx_seo/extended/meta/pager_in_lp_keywords';
    const LP_PAGE_NUM_STRING                             = 'mageworx_seo/extended/meta/page_num_string';
    const XML_PATH_USE_LAYERED_FILTERS_IN_LP_TITLE       = 'mageworx_seo/extended/meta/layered_filters_lp_in_title';
    const XML_PATH_USE_LAYERED_FILTERS_IN_LP_DESCRIPTION = 'mageworx_seo/extended/meta/layered_filters_lp_in_description';
    const XML_PATH_USE_LAYERED_FILTERS_IN_LP_KEYWORDS    = 'mageworx_seo/extended/meta/layered_filters_lp_in_keywords';
    /**#@- */

    /**
     * @param null|int $store
     * @return bool
     */
    public function isUseSeoForCategoryFilters($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_USE_SEO_FILTERS,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int|null $store
     * @return bool
     */
    public function isUseOnSingleFilterOnly($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_USE_SEO_ON_SINGLE_FILTER,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     *
     * @param int|null $storeId
     * @return string
     */
    public function getAddPageNumToMetaTitle($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ADD_PAGER_NUM_IN_TITLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isAddPageNumToMetaTitleDisable($storeId = null)
    {
        return AddPageNum::PAGE_NUM_NO_ADD == $this->getAddPageNumToMetaTitle($storeId);
    }

    /**
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isAddPageNumToBeginningMetaTitle($storeId = null)
    {
        return AddPageNum::PAGE_NUM_ADD_TO_BEINNING == $this->getAddPageNumToMetaTitle($storeId);
    }

    /**
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isAddPageNumToEndMetaTitle($storeId = null)
    {
        return AddPageNum::PAGE_NUM_ADD_TO_END == $this->getAddPageNumToMetaTitle($storeId);
    }

    /**
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isAddPageNumToMetaDescriptionDisable($storeId = null)
    {
        return AddPageNum::PAGE_NUM_NO_ADD == $this->getAddPageNumToMetaDescription($storeId);
    }

    /**
     *
     * @param int|null $storeId
     * @return string
     */
    public function getAddPageNumToMetaDescription($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ADD_PAGER_NUM_IN_DESCRIPTION,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     *
     * @param int|null $storeId
     * @return string
     */
    public function getAddPageNumToMetaKeywords($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ADD_PAGER_NUM_IN_KEYWORDS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isAddPageNumToBeginningMetaDescription($storeId = null)
    {
        return AddPageNum::PAGE_NUM_ADD_TO_BEINNING == $this->getAddPageNumToMetaDescription($storeId);
    }

    /**
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isAddPageNumToBeginningMetaKeywords($storeId = null)
    {
        return AddPageNum::PAGE_NUM_ADD_TO_BEINNING == $this->getAddPageNumToMetaKeywords($storeId);
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isAddPageNumToEndMetaKeywords($storeId = null)
    {
        return AddPageNum::PAGE_NUM_ADD_TO_END == $this->getAddPageNumToMetaKeywords($storeId);
    }

    /**
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isAddPageNumToEndMetaDescription($storeId = null)
    {
        return AddPageNum::PAGE_NUM_ADD_TO_END == $this->getAddPageNumToMetaDescription($storeId);
    }

    /**
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isCutMagentoPrefixSuffix($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_CUT_MAGENTO_PREFIX_SUFFIX,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     *
     * @param int|null $storeId
     * @return array
     */
    public function getPagesForCutPrefixSuffix($storeId = null)
    {
        if ($this->isCutMagentoPrefixSuffix($storeId)) {
            $pagesString = $this->scopeConfig->getValue(
                self::XML_PATH_CUT_MAGENTO_PREFIX_SUFFIX_PAGES,
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
            $pagesArray = array_filter(preg_split('/\r?\n/', $pagesString));
            $pagesArray = array_map('trim', $pagesArray);
            return array_filter($pagesArray);
        }
        return [];
    }

    /**
     * @param string $fullActionName
     * @param int|null $storeId
     * @return boolean
     */
    public function isCutMagentoPrefixSuffixByPage($fullActionName, $storeId = null)
    {
        if ($this->isCutMagentoPrefixSuffix($storeId)) {
            return in_array($fullActionName, $this->getPagesForCutPrefixSuffix($storeId));
        }
        return false;
    }

    /**
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isAddLayeredFiltersToMetaTitle($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_USE_LAYERED_FILTERS_IN_TITLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isAddLayeredFiltersToMetaDescription($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_USE_LAYERED_FILTERS_IN_DESCRIPTION,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isAddLayeredFiltersToMetaKeywords($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_USE_LAYERED_FILTERS_IN_KEYWORDS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isAddLayeredFiltersToLpMetaTitle($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_USE_LAYERED_FILTERS_IN_LP_TITLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isAddLayeredFiltersToLpMetaDescription($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_USE_LAYERED_FILTERS_IN_LP_DESCRIPTION,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     *
     * @param int|null $storeId
     * @return string
     */
    public function getAddPageNumToLpMetaTitle($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ADD_PAGER_NUM_IN_LP_TITLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isAddPageNumToLpMetaTitleDisable($storeId = null)
    {
        return AddPageNum::PAGE_NUM_NO_ADD == $this->getAddPageNumToLpMetaTitle($storeId);
    }

    /**
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isAddPageNumToLpBeginningMetaTitle($storeId = null)
    {
        return AddPageNum::PAGE_NUM_ADD_TO_BEINNING == $this->getAddPageNumToLpMetaTitle($storeId);
    }

    /**
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isAddPageNumToLpEndMetaTitle($storeId = null)
    {
        return AddPageNum::PAGE_NUM_ADD_TO_END == $this->getAddPageNumToLpMetaTitle($storeId);
    }

    /**
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isAddPageNumToLpMetaDescriptionDisable($storeId = null)
    {
        return AddPageNum::PAGE_NUM_NO_ADD == $this->getAddPageNumToLpMetaDescription($storeId);
    }

    /**
     *
     * @param int|null $storeId
     * @return string
     */
    public function getAddPageNumToLpMetaDescription($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ADD_PAGER_NUM_IN_LP_DESCRIPTION,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isAddPageNumToLpBeginningMetaDescription($storeId = null)
    {
        return AddPageNum::PAGE_NUM_ADD_TO_BEINNING == $this->getAddPageNumToLpMetaDescription($storeId);
    }

    /**
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isAddPageNumToLpEndMetaDescription($storeId = null)
    {
        return AddPageNum::PAGE_NUM_ADD_TO_END == $this->getAddPageNumToLpMetaDescription($storeId);
    }

    /**
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isAddLayeredFiltersToLpMetaKeywords($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_USE_LAYERED_FILTERS_IN_LP_KEYWORDS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     *
     * @param int|null $storeId
     * @return string
     */
    public function getAddPageNumToLpMetaKeywords($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ADD_PAGER_NUM_IN_LP_KEYWORDS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isAddPageNumToLpBeginningMetaKeywords($storeId = null)
    {
        return AddPageNum::PAGE_NUM_ADD_TO_BEINNING == $this->getAddPageNumToLpMetaKeywords($storeId);
    }

    /**
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isAddPageNumToLpEndMetaKeywords($storeId = null)
    {
        return AddPageNum::PAGE_NUM_ADD_TO_END == $this->getAddPageNumToLpMetaKeywords($storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getPageNumString($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::LP_PAGE_NUM_STRING,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
