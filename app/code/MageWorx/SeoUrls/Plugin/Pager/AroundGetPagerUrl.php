<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoUrls\Plugin\Pager;

use Magento\Framework\View\Element\Template;

class AroundGetPagerUrl
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
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * Seo Url Builder
     *
     * @var \MageWorx\SeoUrls\Helper\SeoUrlBuilder
     */
    protected $seoUrlBuilder;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * AroundGetPagerUrl constructor.
     * @param \MageWorx\SeoUrls\Helper\Data $helperData
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \MageWorx\SeoUrls\Helper\SeoUrlBuilder $seoUrlBuilder
     */
    public function __construct(
        \MageWorx\SeoUrls\Helper\Data $helperData,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Framework\UrlInterface $urlBuilder,
        \MageWorx\SeoUrls\Helper\SeoUrlBuilder $seoUrlBuilder,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->helperData = $helperData;
        $this->urlHelper  = $urlHelper;
        $this->urlBuilder = $urlBuilder;
        $this->seoUrlBuilder = $seoUrlBuilder;
        $this->request = $request;
    }

    /**
     * @param Template $subject
     * @param $proceed
     * @param array $params
     * @return string
     */
    public function aroundGetPagerUrl(Template $subject, $proceed, $params = [])
    {
        $pagerVarName = $this->helperData->getPagerVariableName();

        // For toolbar URLs
        if (empty($params[$pagerVarName])) {
            $currentPage = $this->request->getParam($pagerVarName);

            if ($currentPage) {
                $params[$pagerVarName] = $currentPage;
            }
        }

        return $this->seoUrlBuilder->getPagerUrl($proceed($params), $params);
    }
}
