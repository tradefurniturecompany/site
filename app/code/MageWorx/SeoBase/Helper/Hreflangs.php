<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoBase\Helper;

use MageWorx\SeoBase\Helper\Data as HelperData;

class Hreflangs extends \Magento\Framework\App\Helper\AbstractHelper
{
    const SCOPE_GLOBAL  = 0;
    const SCOPE_WEBSITE = 1;

    const CMS_RELATION_BY_ID         = 0;
    const CMS_RELATION_BY_URLKEY     = 1;
    const CMS_RELATION_BY_IDENTIFIER = 2;

    /**
     *
     * @var \MageWorx\SeoBase\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        HelperData $helperData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->helperData   = $helperData;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @param type $page
     * @return int|string
     */
    public function getCmsIdentifierValue($page)
    {
        if (!is_object($page)) {
            return null;
        }
        if ($this->helperData->getCmsPageRelationWay() == self::CMS_RELATION_BY_ID) {
            return $page->getPageId();
        }
        if ($this->helperData->getCmsPageRelationWay() == self::CMS_RELATION_BY_URLKEY) {
            return $page->getIdentifier();
        }
        if ($this->helperData->getCmsPageRelationWay() == self::CMS_RELATION_BY_IDENTIFIER) {
            return $page->getMageworxHreflangIdentifier();
        }
    }

    /**
     *
     * @param string $type
     * @param int|null $storeId
     * @return array
     */
    public function getHreflangFinalCodes($type, $storeId = null)
    {
        if (!$storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        $langCodes = $this->getHreflangLanguageCodes($type, $storeId);
        $countryCodes = $this->gethreflangCountryCodes($type, $storeId);

        $hreflangFinalCodes = [];
        $xdefaultStoreIds    = $this->getXDefaultValidStoreIds($type, $storeId);
        $xdefaultStoreId     = array_shift($xdefaultStoreIds);

        foreach ($langCodes as $storeId => $langCode) {
            if (!empty($countryCodes[$storeId])) {
                $langCode = $langCode . '-' . $countryCodes[$storeId];
            }
            if ($storeId == $xdefaultStoreId) {
                $langCode = 'x-default';
            }
            $hreflangFinalCodes[$storeId] = $langCode;
        }

        return $this->deleteDuplicateCodes($hreflangFinalCodes);
    }

    /**
     * @param array $array
     * @return array
     */
    protected function deleteDuplicateCodes($array)
    {
        return array_unique($array);
    }

    /**
     *
     * @param string $type
     * @param int|null $storeId
     * @return string
     */
    public function getHreflangRawCodes($type, $storeId = null)
    {
        if (!$storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        $langCodes    = $this->getHreflangLanguageCodes($type, $storeId);
        $countryCodes = $this->getHreflangCountryCodes($type, $storeId);

        $hreflangRawCodes = [];
        $xdefaultStoreIds = $this->getXDefaultValidStoreIds($type, $storeId);

        foreach ($langCodes as $storeId => $langCode) {
            if (!empty($countryCodes[$storeId])) {
                $langCode = $langCode . '-' . $countryCodes[$storeId];
            }
            if (in_array($storeId, $xdefaultStoreIds)) {
                $langCode = 'x-default';
            }
            $hreflangRawCodes[$storeId] = $langCode;
        }

        return $hreflangRawCodes;
    }

    /**
     *
     * @param string $type
     * @param int|null $storeId
     * @return array
     */
    public function getHreflangLanguageCodes($type, $storeId = null)
    {
        if (!$storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        $storeLangCodes = [];
        $storeIds = $this->getHreflangStoreIds($type, $storeId);

        foreach ($storeIds as $storeId) {
            $storeLangCodes[$storeId] = $this->helperData->getLanguageCode($storeId);
        }

        return $storeLangCodes;
    }

    /**
     *
     * @param string $type
     * @param int|null $storeId
     * @return array
     */
    public function getHreflangCountryCodes($type, $storeId = null)
    {
        if (!$storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        $storeCountryCodes = [];
        $storeIds = $this->getHreflangStoreIds($type, $storeId);

        foreach ($storeIds as $storeId) {
            if ($this->helperData->isCountryCodeEnabled($storeId)) {
                $storeCountryCodes[$storeId] = $this->helperData->getCountryCode($storeId);
            }
        }

        return $storeCountryCodes;
    }

    public function getHreflangStoreIds($type, $storeId)
    {
        if (self::SCOPE_GLOBAL == $this->helperData->getHreflangScope()) {
            return $this->getAllEnabledStoreIdsByType($type);
        }
        return $this->getWebsiteStoreIdsByStoreId($type, $storeId);
    }

    /**
     *
     * @param string $type
     * @param int $storeId
     * @return array
     */
    public function getXDefaultValidStoreIds($type, $storeId)
    {
        $xdefaultStoreIds = $this->helperData->getXDefaultStoreIds();
        if (self::SCOPE_GLOBAL == $this->helperData->getHreflangScope()) {
            $validStoreIds = $this->getAllEnabledStoreIdsByType($type);
            $storeIds = array_intersect($xdefaultStoreIds, $validStoreIds);
        } else {
            $websiteStoreIds  = $this->getWebsiteStoreIdsByStoreId($type, $storeId);
            $storeIds = array_intersect($xdefaultStoreIds, $websiteStoreIds);
        }
        return $storeIds;
    }

    /**
     *
     * @param string $type
     * @param int|null $storeId
     * @return array
     */
    public function getWebsiteStoreIdsByStoreId($type, $storeId = null)
    {
        $rawStoreIds = $this->storeManager->getStore($storeId)->getWebsite()->getStoreIds();
        return $this->filterValidStoreIds($type, $rawStoreIds);
    }

    /**
     *
     * @param string $type
     * @param array $storeIds
     * @return array
     */
    public function filterValidStoreIds($type, $storeIds)
    {
        $validIds = $this->getAllEnabledStoreIdsByType($type);
        return array_intersect($storeIds, $validIds);
    }

    /**
     *
     * @param string $type
     * @return array
     */
    public function getAllEnabledStoreIdsByType($type)
    {
        return array_keys($this->getAllEnabledStoreByType($type));
    }

    /**
     *
     * @param string $type
     * @return array
     */
    public function getAllEnabledStoreByType($type)
    {
        $allStores = $this->getActiveStores();
        $stores    = [];
        foreach ($allStores as $storeId => $store) {
            $storeId = $store->getStoreId();
            if ($this->helperData->isHreflangsEnabledFor($type, $storeId)) {
                $stores[$storeId] = $store;
            }
        }

        return $stores;
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
}
