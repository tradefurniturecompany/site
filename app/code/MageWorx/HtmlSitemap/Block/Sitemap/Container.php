<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\HtmlSitemap\Block\Sitemap;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use MageWorx\HtmlSitemap\Helper\Data as SitemapHelper;

/**
 * Container for sitemap output
 */
class Container extends Template
{
    /**
     * @var \MageWorx\HtmlSitemap\Helper\Data
     */
    protected $sitemapHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \MageWorx\HtmlSitemap\Helper\Data $sitemapHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        SitemapHelper $sitemapHelper,
        array $data = []
    ) {
    
        $this->sitemapHelper = $sitemapHelper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve config data helper
     *
     * @return \MageWorx\HtmlSitemap\Helper\Data
     */
    public function getSitemapHelper()
    {
        return $this->sitemapHelper;
    }

    /**
     * @return bool
     * @see \MageWorx\HtmlSitemap\Helper\Data
     */
    public function isShowStores()
    {
        return $this->getSitemapHelper()->isShowStores();
    }

    /**
     * @return bool
     * @see \MageWorx\HtmlSitemap\Helper\Data
     */
    public function isShowCategories()
    {
        return $this->getSitemapHelper()->isShowCategories();
    }

    /**
     * @return bool
     * @see \MageWorx\HtmlSitemap\Helper\Data
     */
    public function isShowProducts()
    {
        return $this->getSitemapHelper()->isShowProducts();
    }

    /**
     * @return bool
     * @see \MageWorx\HtmlSitemap\Helper\Data
     */
    public function isShowCmsPages()
    {
        return $this->getSitemapHelper()->isShowCmsPages();
    }

    /**
     * @return bool
     * @see \MageWorx\HtmlSitemap\Helper\Data
     */
    public function isShowLinks()
    {
        return $this->getSitemapHelper()->isShowLinks();
    }

    /**
     * @return bool
     * @see \MageWorx\HtmlSitemap\Helper\Data
     */
    public function isShowCustomLinks()
    {
        return $this->getSitemapHelper()->isShowCustomLinks();
    }
}
