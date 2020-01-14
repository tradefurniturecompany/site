<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

/**
 * SEO base store url helper
 *
 */
namespace MageWorx\SeoBase\Helper;

class StoreUrl extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \MageWorx\SeoBase\Helper\Data
     */
    protected $helperData;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageWorx\SeoBase\Helper\Data $helperData
    ) {
        parent::__construct(
            $context
        );
        $this->storeManager = $storeManager;
        $this->helperData   = $helperData;
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
     * Get url
     *
     * @param string $url
     * @param int $storeId
     * @param string $type
     * @return string
     */
    public function getUrl(
        $url,
        $storeId = null,
        $isModifyTrailingSlash = false,
        $isHomePage = false,
        $type = \Magento\Framework\UrlInterface::URL_TYPE_LINK
    ) {
        $url = $this->getStoreBaseUrl($storeId, $type) . ltrim($url, '/');
        return $isModifyTrailingSlash ? $this->trailingSlash($url, $storeId, $isHomePage) : $url;
    }

    /**
     * Retrieve list of active stores
     *
     * @return array
     */
    public function getActiveStores()
    {
        $stores = [];
        foreach ($this->storeManager->getStores() as $store) {
            if ($store->getIsActive()) {
                $stores[$store->getId()] = $store;
            }
        }
        return $stores;
    }

    /**
     * Check if store is active by store ID
     *
     * @param int $id
     * @return bool
     */
    public function isActiveStore($id)
    {
        $this->getActiveStores();
        return array_key_exists($id, $this->getActiveStores());
    }

    /**
     * @return int
     */
    public function getCurrentStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * Crop or add trailing slash
     *
     * @param string $url
     * @param int|null $storeId
     * @param boolean $isHomePage
     * @return string
     */
    public function trailingSlash($url, $storeId = null, $isHomePage = false)
    {
        if ($isHomePage) {
            $trailingSlash = $this->helperData->getTrailingSlashForHomePage($storeId);
        } else {
            $trailingSlash = $this->helperData->getTrailingSlash($storeId);
        }

        if ($trailingSlash == \MageWorx\SeoBase\Model\Source\AddCrop::TRAILING_SLASH_ADD) {
            $url        = rtrim($url);
            $extensions = ['rss', 'html', 'htm', 'xml', 'php'];
            if (substr($url, -1) != '/' && !in_array(substr(strrchr($url, '.'), 1), $extensions)) {
                $url.= '/';
            }
        } elseif ($trailingSlash == \MageWorx\SeoBase\Model\Source\AddCrop::TRAILING_SLASH_CROP) {
            $url = rtrim(rtrim($url), '/');
        }

        return $url;
    }
}
