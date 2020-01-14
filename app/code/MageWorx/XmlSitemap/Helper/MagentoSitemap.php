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

class MagentoSitemap extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Error email template
     */
    const XML_PATH_ERROR_TEMPLATE = 'sitemap/generate/error_email_template';

    /**
     * Enable/disable
     */
    const XML_PATH_GENERATION_ENABLED = 'sitemap/generate/enabled';

    /**
     * 'Send error emails to'
     */
    const XML_PATH_ERROR_RECIPIENT = 'sitemap/generate/error_email';

    /**
     * Error email identity
     */
    const XML_PATH_ERROR_IDENTITY = 'sitemap/generate/error_email_identity';

    /**
     * Get error email template
     *
     * @param int $storeId
     * @return string
     */
    public function getErrorEmailTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ERROR_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Enable/disable
     *
     * @param int $storeId
     * @return bool
     */
    public function isGenerationEnabled($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_GENERATION_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * 'Send error emails to'
     *
     * @param int $storeId
     * @return string
     */
    public function getErrorRecipient($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ERROR_RECIPIENT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * 'Send error emails to'
     *
     * @param int $storeId
     * @return string
     */
    public function getErrorIdentity($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ERROR_IDENTITY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}