<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoAll\Helper;

/**
 * SEO All length helper
 */
class Length extends \Magento\Framework\App\Helper\AbstractHelper
{

    const XML_PATH_LENGTH_META_TITLE_MAX       = 'mageworx_seo/all/length/meta_title_max';
    const XML_PATH_LENGTH_META_DESCRIPTION_MAX = 'mageworx_seo/all/length/meta_description_max';
    const XML_PATH_LENGTH_META_KEYWORDS_MAX    = 'mageworx_seo/all/length/meta_keywords_max';
    const XML_PATH_LENGTH_H1_MAX               = 'mageworx_seo/all/length/h1_max';
    const XML_PATH_LENGTH_URL_PATH_MAX         = 'mageworx_seo/all/length/url_path_max';

    /**
     * @return int
     */
    public function getMetaTitleMaxLength()
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_LENGTH_META_TITLE_MAX
        );
    }


    /**
     * @return int
     */
    public function getMetaDescriptionMaxLength()
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_LENGTH_META_DESCRIPTION_MAX
        );
    }

    /**
     * @return int
     */
    public function getMetaKeywordsMaxLength()
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_LENGTH_META_KEYWORDS_MAX
        );
    }

    /**
     * @return int
     */
    public function getH1MaxLength()
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_LENGTH_H1_MAX
        );
    }

    /**
     * @return int
     */
    public function getUrlMaxLength()
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_LENGTH_URL_PATH_MAX
        );
    }
}
