<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Cron;

class GenerateProductTemplate extends GenerateTemplate
{
    /**
     * Dispatch event for product meta title
     */
    public function generateMetaTitle()
    {
        $this->generateEntityByTypeId(\MageWorx\SeoXTemplates\Model\Template\Product::TYPE_PRODUCT_META_TITLE);
    }

    /**
     * Dispatch event for product meta description
     */
    public function generateMetaDescription()
    {
        $this->generateEntityByTypeId(\MageWorx\SeoXTemplates\Model\Template\Product::TYPE_PRODUCT_META_DESCRIPTION);
    }

    /**
     * Dispatch event for product meta keywords
     */
    public function generateMetaKeywords()
    {
        $this->generateEntityByTypeId(\MageWorx\SeoXTemplates\Model\Template\Product::TYPE_PRODUCT_META_KEYWORDS);
    }

    /**
     * Dispatch event for product SEO name
     */
    public function generateSeoName()
    {
        $this->generateEntityByTypeId(\MageWorx\SeoXTemplates\Model\Template\Product::TYPE_PRODUCT_SEO_NAME);
    }

    /**
     * Dispatch event for product URL
     */
    public function generateUrl()
    {
        $this->generateEntityByTypeId(\MageWorx\SeoXTemplates\Model\Template\Product::TYPE_PRODUCT_URL_KEY);
    }

    /**
     * Dispatch event for product description
     */
    public function generateDescription()
    {
        $this->generateEntityByTypeId(\MageWorx\SeoXTemplates\Model\Template\Product::TYPE_PRODUCT_DESCRIPTION);
    }

    /**
     * Dispatch event for product short description
     */
    public function generateShortDescription()
    {
        $this->generateEntityByTypeId(\MageWorx\SeoXTemplates\Model\Template\Product::TYPE_PRODUCT_SHORT_DESCRIPTION);
    }

    /**
     * Dispatch event for product gallery
     */
    public function generateGallery()
    {
        $this->generateEntityByTypeId(\MageWorx\SeoXTemplates\Model\Template\Product::TYPE_PRODUCT_GALLERY);
    }
}
