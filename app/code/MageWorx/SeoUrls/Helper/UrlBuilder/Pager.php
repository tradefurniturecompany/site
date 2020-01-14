<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoUrls\Helper\UrlBuilder;

use MageWorx\SeoUrls\Model\Source\PagerMask;

class Pager
{
    /**
     * @var \MageWorx\SeoUrls\Helper\Data $helperData
     */
    protected $helperData;

    /**
     * @var \MageWorx\SeoUrls\Helper\Layer
     */
    protected $helperLayer;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \MageWorx\SeoUrls\Helper\Url
     */
    protected $helperUrl;

    /**
     * Pager constructor.
     * @param \MageWorx\SeoUrls\Helper\Data $helperData
     * @param \MageWorx\SeoUrls\Helper\Layer $helperLayer
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \MageWorx\SeoUrls\Helper\Url $helperUrl
     */
    public function __construct(
        \MageWorx\SeoUrls\Helper\Data $helperData,
        \MageWorx\SeoUrls\Helper\Layer $helperLayer,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\UrlInterface $urlBuilder,
        \MageWorx\SeoUrls\Helper\Url $helperUrl
    ) {
    
        $this->helperData = $helperData;
        $this->helperLayer = $helperLayer;
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
        $this->helperUrl = $helperUrl;
    }

    /**
     * @param string $originalUrl
     * @param array $params
     * @return string
     */
    public function getPagerUrl($originalUrl, $params)
    {
        $pagerVarName = $this->helperData->getPagerVariableName();

        if (!empty($params[$pagerVarName])) {
            $pagerUrlFormat = $this->helperData->getPagerUrlFormat();
            $pageNum = $params[$pagerVarName];

            if ($pageNum) {
                $url = str_replace('&amp;', '&', $originalUrl);
                $url = $this->helperUrl->removeRequestParam($url, $pagerVarName);
                $categorySuffix = $this->helperData->getSuffix();
                $urlParts = explode('?', $url);
                $urlParts[0] = $this->addPagerToUrl($urlParts[0], $pageNum, $pagerUrlFormat, $categorySuffix);
                $url = implode('?', $urlParts);
                $url = str_replace('&', '&amp;', $url);
            }
        }
        return empty($url) ? $originalUrl : $url;
    }

    /**
     * @param string $url
     * @param int $pageNum
     * @param string $pagerUrlFormat
     * @param string $suffix
     * @return string
     */
    protected function addPagerToUrl($url, $pageNum, $pagerUrlFormat, $suffix)
    {
        if ($pageNum > 1) {
            $url = $this->helperUrl->removeSuffix($url, $suffix);
            $url .= str_replace(PagerMask::PAGER_NUM_MASK, $pageNum, $pagerUrlFormat);
            $url = $this->helperUrl->addSuffix($url, $suffix);
        }
        return $url;
    }
}
