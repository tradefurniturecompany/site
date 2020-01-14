<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoBase\Model\Hreflangs;

use MageWorx\SeoBase\Helper\Data as HelperData;
use MageWorx\SeoBase\Helper\Hreflangs as HelperHreflangs;
use MageWorx\SeoBase\Helper\Url as HelperUrl;
use MageWorx\SeoBase\Helper\StoreUrl as HelperStore;
use Magento\Framework\UrlInterface;

class HomePage extends \MageWorx\SeoBase\Model\Hreflangs
{
    /**
     * @var \MageWorx\SeoBase\Helper\StoreUrl
     */
    protected $helperStore;

    /**
     *
     * @var \MageWorx\SeoBase\Helper\Hreflangs
     */
    protected $helperHreflangs;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @var \MageWorx\SeoBase\Model\ResourceModel\Cms\Page\HreflangsFactory
     */
    protected $hreflangFactory;

    /**
     *
     * @var \Magento\Framework\View\Layout;
     */
    protected $layout;

    /**
     *
     * @param HelperData $helperData
     * @param HelperUrl $helperUrl
     * @param HelperStore $helperStore
     * @param HelperHreflangs $helperHreflangs
     * @param UrlInterface $url
     * @param \MageWorx\SeoBase\Model\ResourceModel\Cms\Page\HreflangsFactory $hreflangFactory
     * @param \Magento\Framework\View\Layout $layout
     * @param string $fullActionName
     */
    public function __construct(
        HelperData $helperData,
        HelperUrl $helperUrl,
        HelperStore $helperStore,
        HelperHreflangs $helperHreflangs,
        UrlInterface $url,
        \MageWorx\SeoBase\Model\ResourceModel\Cms\Page\HreflangsFactory $hreflangFactory,
        \Magento\Framework\View\Layout $layout,
        $fullActionName
    ) {
        $this->helperStore        = $helperStore;
        $this->url                = $url;
        $this->helperHreflangs    = $helperHreflangs;
        $this->hreflangFactory    = $hreflangFactory;
        $this->layout             = $layout;
        parent::__construct($helperData, $helperUrl, $fullActionName);
    }

    /**
     * {@inheritdoc}
     */
    public function getHreflangUrls()
    {
        if ($this->isCancelHreflangs()) {
            return null;
        }

        $page       = $this->getPage();
        $pageId     = (empty($page) || !is_object($page)) ? 0 : $page->getId();
        $currentUrl = $this->url->getCurrentUrl();

        if (strpos($currentUrl, '?') !== false) {
            return null;
        }

        $hreflangCodes = $this->helperHreflangs->getHreflangFinalCodes('cms');

        if (empty($hreflangCodes)) {
            return null;
        }

        $hreflangResource = $this->hreflangFactory->create();
        $hreflangUrlsData = $hreflangResource->getHreflangsDataForHomePage(array_keys($hreflangCodes), $pageId, true);

        if (empty($hreflangUrlsData[$pageId]['hreflangUrls'])) {
            return null;
        }

        $hreflangUrls = [];
        foreach ($hreflangUrlsData[$pageId]['hreflangUrls'] as $store => $altUrl) {
            $hreflang = $hreflangCodes[$store];
            $hreflangUrls[$hreflang] = $altUrl;
        }
        return (!empty($hreflangUrls)) ? $hreflangUrls : null;
    }

    protected function getPage()
    {
        $block = $this->layout->getBlock('cms_page');
        if (is_object($block)) {
            return $block->getPage();
        }
        return null;
    }
}
