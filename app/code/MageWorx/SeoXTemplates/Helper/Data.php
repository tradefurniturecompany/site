<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Helper;

/**
 * SEO XTemplates data helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const MAX_DEFAULT_LENGTH_META_TITLE        = 70;
    const MAX_DEFAULT_LENGTH_META_DESCRIPTION  = 150;

    const XML_PATH_TEMPLATE_LIMIT              = 200;
    const XML_PATH_CROP_ROOT_CATEGORY          = 'mageworx_seo/seoxtemplates/crop_root_category';

    const XML_PATH_ENABLE_PRODUCT_SEO_NAME     = 'mageworx_seo/seoxtemplates/use_product_seo_name';
    const XML_PATH_ENABLE_CATEGORY_SEO_NAME    = 'mageworx_seo/seoxtemplates/use_category_seo_name';

    const XML_PATH_CROP_META_TITLE             = 'mageworx_seo/seoxtemplates/crop_meta_title';
    const XML_PATH_CROP_META_DESCRIPTION       = 'mageworx_seo/seoxtemplates/crop_meta_description';
    const XML_PATH_MAX_LENGTH_META_TITLE       = 'mageworx_seo/seoxtemplates/max_title_length';
    const XML_PATH_MAX_LENGTH_META_DESCRIPTION = 'mageworx_seo/seoxtemplates/max_description_length';

    const XML_PATH_ENABLE_CRON_NOTIFY          = 'mageworx_seo/seoxtemplates/enabled_cron_notify';
    const XML_PATH_ERROR_TEMPLATE              = 'mageworx_seo/seoxtemplates/error_email_template';
    const XML_PATH_ERROR_IDENTITY              = 'mageworx_seo/seoxtemplates/error_email_identity';
    const XML_PATH_ERROR_RECIPIENT             = 'mageworx_seo/seoxtemplates/error_email';

    /**
     * @param int|null $store
     * @return bool
     */
    public function isCropMetaTitle($store = null)
    {
        return (bool) $this->scopeConfig->getValue(
            self::XML_PATH_CROP_META_TITLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Retrieve max length for meta title for specified view mode
     *
     * @param int|null $store
     * @return int
     */
    public function getMaxLengthMetaTitle($store = null)
    {
        $max = (int) $this->scopeConfig->getValue(
            self::XML_PATH_MAX_LENGTH_META_TITLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        if (!$max) {
            return self::MAX_DEFAULT_LENGTH_META_TITLE;
        }
        return $max;
    }

    /**
     * @param int|null $store
     * @return bool
     */
    public function isCropMetaDescription($store = null)
    {
        return (bool) $this->scopeConfig->getValue(
            self::XML_PATH_CROP_META_DESCRIPTION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Retrieve max length for meta description for specified view mode
     *
     * @param int|null $store
     * @return int
     */
    public function getMaxLengthMetaDescription($store = null)
    {
        $max = (int) $this->scopeConfig->getValue(
            self::XML_PATH_MAX_LENGTH_META_DESCRIPTION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        if (!$max) {
            return self::MAX_DEFAULT_LENGTH_META_DESCRIPTION;
        }
        return $max;
    }

    /**
     * Use product seo name attribute instead name
     *
     * @param int|null $store
     * @return bool
     */
    public function isUseProductSeoName($store = null)
    {
        return (bool) $this->scopeConfig->getValue(
            self::XML_PATH_ENABLE_PRODUCT_SEO_NAME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Use category seo name attribute instead name
     *
     * @param int|null $store
     * @return bool
     */
    public function isUseCategorySeoName($store = null)
    {
        return (bool) $this->scopeConfig->getValue(
            self::XML_PATH_ENABLE_CATEGORY_SEO_NAME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Retrieve quantity of templates items for a generation step
     *
     * @return int
     */
    public function getTemplateLimitForCurrentStore()
    {
        return self::XML_PATH_TEMPLATE_LIMIT;
    }

    /**
     * Is crop root category from template
     *
     * @param int|null $store
     * @return bool
     */
    public function isCropRootCategory($store = null)
    {
        return (bool) $this->scopeConfig->getValue(
            self::XML_PATH_CROP_ROOT_CATEGORY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Is enabled e-mail notification for generation by cron
     * @param int|null $store
     * @return bool
     */
    public function isEnabledCronNotify($store = null)
    {
        return (bool) $this->scopeConfig->getValue(
            self::XML_PATH_ENABLE_CRON_NOTIFY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     *
     * @param int|null $store
     * @return string
     */
    public function getErrorEmailTemplate()
    {
        return 'mageworx_seoxtemplates_generate_error_email_template';
    }

    /**
     *
     * @param int|null $store
     * @return string
     */
    public function getErrorEmailIdentity($store = null)
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_ERROR_IDENTITY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     *
     * @param int|null $store
     * @return string
     */
    public function getErrorEmailRecipient($store = null)
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_ERROR_RECIPIENT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     *
     * @param int|null $store
     * @return bool
     */
    public function isShowCommentAboutCategory($store = null)
    {
        if (!$this->useCategoriesPathInProductUrl($store)) {
            return true;
        }
        return false;
    }

    /**
     *
     * @param int|null $store
     * @return string
     */
    public function useCategoriesPathInProductUrl($store = null)
    {
        return (bool) $this->scopeConfig->getValue(
            \Magento\Catalog\Helper\Product::XML_PATH_PRODUCT_URL_USE_CATEGORY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     *
     * @param int|null $store
     * @return string
     */
    public function getTitleSeparator($store)
    {
        $separator = $this->scopeConfig->getValue(
            'catalog/seo/title_separator',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );

        if ($separator === ',') {
            return $separator . ' ';
        }

        return ' ' . $separator . ' ';
    }
}
