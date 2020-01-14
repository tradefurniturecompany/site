<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoUrls\Plugin\LayerFilterItem;

class AfterGetUrl
{
    /**
     * @var \MageWorx\SeoUrls\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\SeoUrls\Helper\UrlBuildWrapper
     */
    protected $urlBuildWrapper;

    /**
     * AfterGetUrl constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \MageWorx\SeoUrls\Helper\Data $helperData
     * @param \MageWorx\SeoUrls\Helper\UrlBuildWrapper $urlBuildWrapper
     */
    public function __construct(
        \MageWorx\SeoUrls\Helper\Data $helperData,
        \MageWorx\SeoUrls\Helper\UrlBuildWrapper $urlBuildWrapper
    ) {
        $this->helperData         = $helperData;
        $this->urlBuildWrapper    = $urlBuildWrapper;
    }

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\Item $filterItem
     * @param $url
     * @return string
     */
    public function afterGetUrl(\Magento\Catalog\Model\Layer\Filter\Item $filterItem, $url)
    {
        if ($this->out()) {
            return $url;
        }

        return $this->urlBuildWrapper->getFilterUrl($filterItem);
    }

    /**
     * @return bool
     */
    protected function out()
    {
        if (!$this->helperData->getIsSeoFiltersEnable()) {
            return true;
        }

        return !$this->helperData->getIsCompatiblePage();
    }
}
