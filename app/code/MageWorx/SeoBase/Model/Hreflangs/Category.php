<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoBase\Model\Hreflangs;

use MageWorx\SeoBase\Model\ResourceModel\Catalog\Category\HreflangsFactory;
use MageWorx\SeoBase\Helper\Data as HelperData;
use MageWorx\SeoBase\Helper\Hreflangs as HelperHreflangs;
use MageWorx\SeoBase\Helper\Url as HelperUrl;
use MageWorx\SeoBase\Helper\StoreUrl as HelperStore;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;

class Category extends \MageWorx\SeoBase\Model\Hreflangs
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
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @var \MageWorx\SeoBase\Model\ResourceModel\Catalog\Category\HreflangFactory
     */
    protected $hreflangFactory;


    /**
     * @var \Magento\Catalog\Model\Layer\Category\
     */
    protected $catalogLayer;

    /**
     *
     * @param HelperData $helperData
     * @param HelperUrl $helperUrl
     * @param HelperStore $helperStore
     * @param HelperHreflangs $helperHreflangs
     * @param Registry $registry
     * @param UrlInterface $url
     * @param HreflangsFactory $hreflangFactory
     * @param LayerResolver $layerResolver
     * @param string $fullActionName
     */
    public function __construct(
        HelperData $helperData,
        HelperUrl $helperUrl,
        HelperStore $helperStore,
        HelperHreflangs $helperHreflangs,
        Registry $registry,
        UrlInterface $url,
        HreflangsFactory $hreflangFactory,
        LayerResolver $layerResolver,
        $fullActionName
    ) {

        $this->registry           = $registry;
        $this->helperStore        = $helperStore;
        $this->url                = $url;
        $this->catalogLayer       = $layerResolver->get();
        $this->helperHreflangs    = $helperHreflangs;
        $this->hreflangFactory = $hreflangFactory;
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

        $category = $this->registry->registry('current_category');
        if (empty($category) || !is_object($category)) {
            return null;
        }

        $categoryId       = $category->getId();
        $currentUrl       = $this->url->getCurrentUrl();
        $isFiltersApplyed = (bool)$this->getLayeredNavigationFiltersCode();

        if (strpos($currentUrl, '?') === false && !$isFiltersApplyed) {
            $hreflangCodes = $this->helperHreflangs->getHreflangFinalCodes('category');
            if (empty($hreflangCodes)) {
                return null;
            }

            $hreflangResource = $this->hreflangFactory->create();
            $hreflangUrlsData = $hreflangResource->getHreflangsData(array_keys($hreflangCodes), $categoryId);

            if (empty($hreflangUrlsData[$categoryId]['hreflangUrls'])) {
                return null;
            }

            $hreflangUrls = [];
            foreach ($hreflangUrlsData[$categoryId]['hreflangUrls'] as $store => $altUrl) {
                if (!empty($hreflangCodes[$store])) {
                    $hreflang = $hreflangCodes[$store];
                    $hreflangUrls[$hreflang] = $altUrl;
                }
            }
        }
        return (!empty($hreflangUrls)) ? $hreflangUrls : null;
    }

    /**
     * Retrieve list of current filter codes
     *
     * @return array
     */
    protected function getLayeredNavigationFiltersCode()
    {
        $filterCodes    = [];
        $appliedFilters = $this->catalogLayer->getState()->getFilters();

        if (is_array($appliedFilters) && count($appliedFilters) > 0) {
            foreach ($appliedFilters as $item) {
                $filterCodes[] = $item->getFilter()->getRequestVar();
            }
        }
        return $filterCodes;
    }
}
