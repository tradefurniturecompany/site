<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\Canonical;

use MageWorx\SeoBase\Api\CustomCanonicalRepositoryInterface;

class Page extends \MageWorx\SeoBase\Model\Canonical
{
    /**
     * @var \Magento\Framework\View\Layout
     */
    protected $layout;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     *
     * @param \MageWorx\SeoBase\Helper\Data $helperData
     * @param \MageWorx\SeoBase\Helper\Url $helperUrl
     * @param \MageWorx\SeoBase\Helper\StoreUrl $helperStoreUrl
     * @param CustomCanonicalRepositoryInterface $customCanonicalRepository
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Framework\View\Layout $layout
     * @param string $fullActionName
     */
    public function __construct(
        \MageWorx\SeoBase\Helper\Data $helperData,
        \MageWorx\SeoBase\Helper\Url  $helperUrl,
        \MageWorx\SeoBase\Helper\StoreUrl $helperStoreUrl,
        CustomCanonicalRepositoryInterface $customCanonicalRepository,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\View\Layout $layout,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $fullActionName
    ) {
        $this->layout = $layout;
        $this->url = $url;
        $this->storeManager = $storeManager;
        parent::__construct($helperData, $helperUrl, $helperStoreUrl, $customCanonicalRepository, $fullActionName);
    }

    /**
     * Retrieve CMS pages canonical URL
     *
     * @return string|null
     */
    public function getCanonicalUrl()
    {
        if ($this->isCancelCanonical()) {
            return null;
        }
        $page       = $this->getPage();
        $pageUrl    = $this->helperStoreUrl->getUrl($page->getData('identifier'));
        $url        = $this->helperUrl->deleteAllParametrsFromUrl($pageUrl);
        
        if ($page) {
            $homePageId = null;
            $homeIdentifier = $this->helperData->getHomeIdentifier();

            if (strpos($homeIdentifier, '|') !== false) {
                list($homeIdentifier, $homePageId) = explode('|', $homeIdentifier);
            }

            if ($homeIdentifier == $page->getIdentifier()) {

                $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
                $storeId = $this->storeManager->getStore()->getStoreId();
                $urlRaw = $this->getStoreBaseUrl($storeId);
                $url = $this->trailingSlash($urlRaw, $storeId, true);
                return $this->helperUrl->escapeUrl($url);
            }
        }

        return $this->renderUrl($url);
    }

    /**
     * Get store base url
     *
     * @param int $storeId
     * @param string $type
     * @return string
     */
    public function getStoreBaseUrl($storeId = null, $type = \Magento\Framework\UrlInterface::URL_TYPE_LINK)
    {
        return rtrim($this->storeManager->getStore($storeId)->getBaseUrl($type), '/') . '/';
    }

    /**
     * Retrieve current CMS page model from layout
     *
     * @return \Magento\Cms\Model\Page|null
     */
    protected function getPage()
    {
        $block = $this->layout->getBlock('cms_page');
        if (is_object($block)) {
            $page = $block->getPage();
            if (is_object($page)) {
                return $page;
            }
        }

        return null;
    }
}
