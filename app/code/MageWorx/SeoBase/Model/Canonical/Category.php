<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\Canonical;

use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use MageWorx\SeoBase\Api\CustomCanonicalRepositoryInterface;

class Category extends \MageWorx\SeoBase\Model\Canonical
{
    /**#@+
     * Canonical URL for layered navigation pages - admin config settings
     */
    const CATEGORY_LN_CANONICAL_OFF          = 0;

    const CATEGORY_LN_CANONICAL_USE_FILTERS  = 1;

    const CATEGORY_LN_CANONICAL_CATEGORY_URL = 2;

    /**#@+
     * Canonical URL for layered navigation pages - attribute individual settings
     */
    const ATTRIBUTE_LN_CANONICAL_BY_CONFIG    = 0;

    const ATTRIBUTE_LN_CANONICAL_USE_FILTERS  = 1;

    const ATTRIBUTE_LN_CANONICAL_CATEGORY_URL = 2;

    /**#@+
     * Canonical URL for layered navigation pages with multiple selection
     */
    const CATEGORY_LN_CANONICAL_MULTIPLE_SELECTION_FILTERED  = 0;

    const CATEGORY_LN_CANONICAL_MULTIPLE_SELECTION_CATEGORY = 2;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @var \MageWorx\SeoBase\Model\ResourceModel\Catalog\Category\CrossDomainFactory
     */
    protected $crossDomainFactory;

    /**
     * @var \MageWorx\SeoBase\Helper\Url
     */
    protected $helperUrl;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $category;

    /**
     * @var \MageWorx\SeoAll\Helper\Layer
     */
    protected $helperLayer;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Category constructor.
     * @param \MageWorx\SeoBase\Helper\Data $helperData
     * @param \MageWorx\SeoBase\Helper\Url $helperUrl
     * @param \MageWorx\SeoBase\Helper\StoreUrl $helperStoreUrl
     * @param CustomCanonicalRepositoryInterface $customCanonicalRepository
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\UrlInterface $url
     * @param \MageWorx\SeoBase\Model\ResourceModel\Catalog\Category\CrossDomainFactory $crossDomainFactory
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\View\Layout $layout
     * @param \MageWorx\SeoAll\Helper\Layer $helperLayer
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $fullActionName
     */
    public function __construct(
        \MageWorx\SeoBase\Helper\Data $helperData,
        \MageWorx\SeoBase\Helper\Url  $helperUrl,
        \MageWorx\SeoBase\Helper\StoreUrl $helperStoreUrl,
        CustomCanonicalRepositoryInterface $customCanonicalRepository,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\UrlInterface $url,
        \MageWorx\SeoBase\Model\ResourceModel\Catalog\Category\CrossDomainFactory $crossDomainFactory,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\View\Layout $layout,
        \MageWorx\SeoAll\Helper\Layer $helperLayer,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $fullActionName
    ) {
        $this->registry           = $registry;
        $this->url                = $url;
        $this->category           = $categoryRepository;
        $this->helperUrl          = $helperUrl;
        $this->layout             = $layout;
        $this->crossDomainFactory = $crossDomainFactory;
        $this->helperLayer        = $helperLayer;
        $this->objectManager      = $objectManager;
        parent::__construct($helperData, $helperUrl, $helperStoreUrl, $customCanonicalRepository, $fullActionName);
    }

    /**
     * Retrieve category canonical URL
     *
     * @return string|null
     */
    public function getCanonicalUrl()
    {
        if ($this->isCancelCanonical()) {
            return null;
        }

        $category = $this->registry->registry('current_category');
        if (empty($category) || !is_object($category)) {
            return null;
        }

        $crossDomainUrl = $this->convertToCrossDomain($category);
        if ($crossDomainUrl) {
            return $this->trailingSlash($crossDomainUrl);
        }

        $url           = $this->url->getCurrentUrl();
        $pageParamName = $this->getPageVarName();

        if ($this->helperLayer->getCurrentLayeredFilters())
        {
            if ($this->helperData->getCanonicalTypeForLayeredPages() == self::CATEGORY_LN_CANONICAL_OFF) {
                return '';
            }
            if (!$this->isIncludeLNFiltersToCanonicalUrl()) {
                return $this->trailingSlash($category->getUrl());
            }

            if ($this->helperData->isSeoUrlsEnable()) {

                if ($this->helperData->usePagerForCanonical()) {
                    $url = $this->changePagerParameterToCurrentForCurrentUrl($url);
                } else {
                    /** @var \MageWorx\SeoUrls\Helper\UrlParser\Pager $pagerParser */
                    $pagerParser = $this->objectManager->get('\MageWorx\SeoUrls\Helper\UrlParser\Pager');
                    $urlData = $pagerParser->parse($url, '');

                    if (!empty($urlData['url'])) {
                        $url = $urlData['url'];
                    }
                }

                $exceptParams = array_merge([$pageParamName], $this->helperLayer->getLayeredNavigationFiltersCode());
                $url = $this->helperUrl->deleteUrlParametrsWithExcept($url, $exceptParams);
            } else {
                $subCategoryUrl = $this->getSubCategoryUrlByCurrentUrl($url);
                if (!is_null($subCategoryUrl)) {
                    $url = $this->convertSubCategoryUrl($url, $subCategoryUrl);
                }

                if ($this->helperData->usePagerForCanonical()) {
                    $url = $this->helperUrl->removeFirstPage($url);
                    $exceptParams = array_merge([$pageParamName], $this->helperLayer->getLayeredNavigationFiltersCode());
                    $url = $this->helperUrl->deleteUrlParametrsWithExcept($url, $exceptParams);
                } else {
                    $url = $this->helperUrl->deleteUrlParametrsWithExcept($url, $this->helperLayer->getLayeredNavigationFiltersCode());
                }
            }
        } else {
            if ($this->helperData->usePagerForCanonical()) {
                $urlRaw = $this->helperUrl->removeFirstPage($url);
                $url = $this->helperUrl->deleteUrlParametrsWithExcept($urlRaw, [$pageParamName]);
                if (strpos($url, '?') === false) {
                    $url = $this->trailingSlash($category->getUrl());
                }
            } else {
                $url = $this->trailingSlash($category->getUrl());
            }
        }

        return $this->helperUrl->escapeUrl($url);
    }

    /**
     * Replace base URL part for canonical URL if cross domain store or URL used
     *
     * @param \Magento\Catalog\Model\Category $category
     * @param string $url
     * @return string|false
     */
    protected function convertToCrossDomain($category)
    {
        $crossDomainStoreByConfig = $this->getCrossDomainStoreId($this->helperData->getCrossDomainStore());
        $crossDomainUrlByConfig   = $this->helperData->getCrossDomainUrl();

        if ($crossDomainStoreByConfig) {
            $crossDomainCategory = $this->crossDomainFactory->create()
                ->getCrossDomainData($category->getId(), $crossDomainStoreByConfig);
            if (is_object($crossDomainCategory)) {
                return $crossDomainCategory->getUrl();
            }
        } elseif ($crossDomainUrlByConfig) {
            $storeBaseUrl = $this->helperStoreUrl->getStoreBaseUrl();
            return str_replace(
                rtrim(
                    $storeBaseUrl,
                    '/'
                ) . '/',
                rtrim(trim($crossDomainUrlByConfig), '/') . '/',
                $category->getUrl()
            );
        }
        return false;
    }

    /**
     * Retrieve current URL with a specified pager: with parameter 'p =' or as URL part: '* [page_number] * '.html? =...
     * Example 1:
     *      Old url from google search: example.com/computers?p=2
     *      Retrive url: example.com/computers-page2.html (If friendly pager ON, etc.)
     * Example 2 (with layered, sort and mode params):
     *      Old url from google search: example.com/electronics/lnav/price:0-1000.html?p=3&limit=15&mode=list
     *      Retrive url:                example.com/electronics/lnav/price:0-1000-page3.html?limit=15&mode=list
     * @return string
     */
    protected function changePagerParameterToCurrentForCurrentUrl($url)
    {
        $pager   = $this->getPager();

        if (!is_object($pager)) {
            return $url;
        }

        $pageNum = $pager->getCurrentPage();

        if (!$pageNum) {
            return $url;
        }

        //If friendly url disable
        //Canonical for ex.com/computers.html?p=1 is ex.com/computers.html?p=1,
        //Canonical for ex.com/computers.html     is ex.com/computers.html
        //If friendly url enable and use custom pager
        //Canonical for ex.com/computers.html     is ex.com/computers.html
        //Canonical for ex.com/computers.html?p=1 is ex.com/computers.html
        //Because for custom pager url we don't use '1'

        if ($pageNum == 1) {
            $url = $this->helperUrl->deleteUrlParametrs($url, [$pager->getPageVarName()]);
        }
        else {
            $url = $pager->getPageUrl($pageNum);
        }
        return $url;
    }

    /**
     * Check if enable layered filters in canonical URL
     *
     * @return boolean
     */
    protected function isIncludeLNFiltersToCanonicalUrl()
    {
        $enableByConfig  = $this->helperData->getCanonicalTypeForLayeredPages();

        if ($this->helperData->getCanonicalTypeForLayeredPagesWithMultipleSelection() ==
            self::CATEGORY_LN_CANONICAL_MULTIPLE_SELECTION_CATEGORY
            && $this->helperLayer->isUsedMultipleSelectionInLayer()
        ) {
            return false;
        }

        $answerByFilters = $this->isIncludeLNFiltersToCanonicalUrlByFilters();

        if ($enableByConfig == self::CATEGORY_LN_CANONICAL_USE_FILTERS
            && $answerByFilters == self::ATTRIBUTE_LN_CANONICAL_CATEGORY_URL
        ) {
            return false;
        }
        if ($enableByConfig == self::CATEGORY_LN_CANONICAL_CATEGORY_URL
            && $answerByFilters == self::ATTRIBUTE_LN_CANONICAL_USE_FILTERS
        ) {
            return true;
        }
        if ($enableByConfig == self::CATEGORY_LN_CANONICAL_USE_FILTERS) {
            return true;
        }
        return false;
    }

    /**
     * Check if enable layered filters in canonical URL by current filters
     *
     * @return int
     */
    protected function isIncludeLNFiltersToCanonicalUrlByFilters()
    {
        $filtersData = $this->helperLayer->getLayeredNavigationFiltersData();

        if (!$filtersData) {
            return self::ATTRIBUTE_LN_CANONICAL_BY_CONFIG;
        }
        usort($filtersData, [$this, "filterSort"]);
        foreach ($filtersData as $data) {
            if (!empty($data['use_in_canonical'])) {
                return $data['use_in_canonical'];
            }
        }
        return false;
    }

    /**
     * @param int $a
     * @param int $b
     * @return int
     */
    protected function filterSort($a, $b)
    {
        $a['position'] = (empty($a['position'])) ? 0 : $a['position'];
        $b['position'] = (empty($b['position'])) ? 0 : $b['position'];

        if ($a['position'] == $b['position']) {
            return 0;
        }
        return ($a['position'] < $b['position']) ? +1 : -1;
    }

    /**
     * Retrieve subcategory URL if input URL content category filter
     *
     * @param string $url
     * @return string|null
     */
    protected function getSubCategoryUrlByCurrentUrl($url)
    {
        $parseUrl = parse_url($url);

        if (empty($parseUrl['query'])) {
            return $url;
        }
        $params = '';
        parse_str(html_entity_decode($parseUrl['query']), $params);
        if (!empty($params['cat']) && is_numeric($params['cat'])) {
            $subCategoryUrl = $this->category->get($params['cat'])->getUrl();
        }
        return (!empty($subCategoryUrl)) ? $subCategoryUrl : null;
    }

    /**
     * Render subcategory URL
     *
     * @param string $url
     * @param string $categoryUrl
     * @return string
     */
    protected function convertSubCategoryUrl($url, $categoryUrl)
    {
        if ($categoryUrl) {
            $parseUrl = parse_url($url);
            if (!empty($parseUrl['query'])) {
                $url      = $categoryUrl . '?' . $parseUrl['query'];
            } else {
                $url      = $categoryUrl;
            }
            $url      = $this->helperUrl->deleteUrlParametrs($url, ['cat']);
        }
        return $url;
    }

    /**
     * Retrieve pager block from layout
     *
     * @return \Magento\Theme\Block\Html\Pager
     */
    protected function getPager()
    {
        if (is_object($this->layout)) {
            return $this->layout->getBlock('product_list_toolbar_pager');
        }
        return null;
    }

    /**
     * Retrieve pager var name
     *
     * @return string
     */
    protected function getPageVarName()
    {
        $pager = $this->getPager();
        if (is_object($pager)) {
            return $pager->getPageVarName() ? $pager->getPageVarName() : 'p';
        }
        return 'p';
    }

    /**
     * Retrieve cross domain store ID
     *
     * @return int
     */
    protected function getCrossDomainStoreId($storeId)
    {
        if (!$storeId) {
            return false;
        }
        if (!$this->helperStoreUrl->isActiveStore($storeId)) {
            return false;
        }
        if ($this->helperStoreUrl->getCurrentStoreId() == $storeId) {
            return false;
        }
        return $storeId;
    }

    /**
     * @param $url
     * @return string
     */
    protected function deleteLimitParams($url)
    {
        $limitVars = [
            $this->getPager()->getLimitVarName(),
            \Magento\Catalog\Model\Product\ProductList\Toolbar::LIMIT_PARAM_NAME
        ];
        return $this->helperUrl->deleteUrlParametrs($url, $limitVars);
    }
}