<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoUrls\Helper;

use Magento\Framework\View\Element\Template;

class SeoUrlBuilder
{
    /**
     * @var \MageWorx\SeoUrls\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \MageWorx\SeoUrls\Helper\UrlBuilder\Layer
     */
    protected $seoLayerUrlBuilder;

    /**
     * @var \MageWorx\SeoUrls\Helper\UrlBuilder\Pager
     */
    protected $seoPagerUrlBuilder;

    /**
     * SeoUrlBuilder constructor.
     * @param Data $helperData
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param UrlBuilder\Layer $seoLayerUrlBuilder
     * @param UrlBuilder\Pager $seoPagerUrlBuilder
     */
    public function __construct(
        \MageWorx\SeoUrls\Helper\Data $helperData,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Framework\UrlInterface $urlBuilder,
        \MageWorx\SeoUrls\Helper\UrlBuilder\Layer $seoLayerUrlBuilder,
        \MageWorx\SeoUrls\Helper\UrlBuilder\Pager $seoPagerUrlBuilder
    ) {
        $this->request    = $request;
        $this->helperData = $helperData;
        $this->urlHelper  = $urlHelper;
        $this->urlBuilder = $urlBuilder;
        $this->seoLayerUrlBuilder = $seoLayerUrlBuilder;
        $this->seoPagerUrlBuilder = $seoPagerUrlBuilder;
    }

    /**
     * @param string $url
     * @param array $params
     * @return string
     */
    public function getPagerUrl($url, $params, $isRedirect = false)
    {
        if ($this->out() && !$isRedirect) {
            return $url;
        }

        $urlParams                 = [];
        $urlParams['_current']     = true;
        $urlParams['_escape']      = true;
        $urlParams['_use_rewrite'] = true;
        $urlParams['_query']       = $params;

        if ($this->helperData->getIsSeoFiltersEnable()) {
            $url = $this->seoLayerUrlBuilder->getLayerFilterUrl($urlParams);
        }

        if ($this->helperData->getIsSeoPagerEnable()) {
            $url = $this->seoPagerUrlBuilder->getPagerUrl($url, $params);
        }

        return $url;
    }

    /**
     * @return bool
     */
    protected function out()
    {
        return !$this->helperData->getIsCompatiblePage();

    }
}
