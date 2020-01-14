<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\HtmlSitemap\Block\Sitemap;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use MageWorx\HtmlSitemap\Helper\Data as SitemapHelper;
use MageWorx\HtmlSitemap\Helper\StoreUrl as StoreUrlHelper;

/**
 * Links block
 */
class Links extends Template
{
    /**
     * @var array
     */
    protected $links;

    /**
     * @var \MageWorx\HtmlSitemap\Helper\StoreUrl
     */
    protected $storeUrlHelper;

    /**
     * @var \MageWorx\HtmlSitemap\Helper\Data
     */
    protected $sitemapHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \MageWorx\HtmlSitemap\Helper\Data
     * @param \MageWorx\HtmlSitemap\Helper\StoreUrl
     * @param array $data
     */
    public function __construct(
        Context $context,
        SitemapHelper         $sitemapHelper,
        StoreUrlHelper        $storeUrlHelper,
        array $data = []
    ) {
        $this->sitemapHelper   = $sitemapHelper;
        $this->storeUrlHelper  = $storeUrlHelper;

        parent::__construct($context, $data);
    }

    /**
     *
     * @return \MageWorx\HtmlSitemap\Block\Sitemap\Links
     */
    protected function _prepareLayout()
    {
        $links = [];
        /** @var array **/
        $addLinks = $this->sitemapHelper->getAdditionalLinks();

        if (count($addLinks)) {
            foreach ($addLinks as $linkString) {
                $link = explode(',', $linkString, 2);
                if (count($link) !== 2) {
                    continue;
                }
                $links[] =
                    [
                        'url'   => $this->buildUrl($link[0]),
                        'title' => htmlspecialchars(strip_tags(trim($link[1])))
                    ];
            }
        }
        $this->links = $links;
        return $this;
    }

    /**
     * Convert URL to store URL if schema don't exist.
     *
     * @param string $rawUrl
     * @return string
     */
    protected function buildUrl($rawUrl)
    {
        $url = trim($rawUrl);
        return (strpos($url, '://') !== false) ? $url : $this->storeUrlHelper->getUrl(ltrim($url, '/'));
    }

    /**
     * Retrieve array of links
     *
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }
}
