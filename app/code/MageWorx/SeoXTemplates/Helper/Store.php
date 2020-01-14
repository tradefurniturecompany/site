<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

/**
 * SEO base store url helper
 *
 */
namespace MageWorx\SeoXTemplates\Helper;

class Store extends \Magento\Framework\App\Helper\AbstractHelper
{
    const SINGLE_STORE_MODE_ENABLED  = 1;
    const SINGLE_STORE_MODE_DISABLED = 0;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct(
            $context
        );
        $this->storeManager = $storeManager;
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
    public function getUrl($url, $storeId = null, $type = \Magento\Framework\UrlInterface::URL_TYPE_LINK)
    {
        return $this->getStoreBaseUrl($storeId, $type) . ltrim($url, '/');
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
}
