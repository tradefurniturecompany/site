<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoMarkup\Helper\Json;

use Magento\Store\Model\StoreRepository;

class LandingPage extends Category
{
    /**
     * @var \MageWorx\SeoMarkup\Helper\LandingPage
     */
    protected $helperLp;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * LandingPage constructor.
     *
     * @param \Magento\Framework\Registry $registry
     * @param \MageWorx\SeoMarkup\Helper\Category $helperCategory
     * @param \MageWorx\SeoMarkup\Helper\LandingPage $helperLp
     * @param \MageWorx\SeoMarkup\Helper\DataProvider\Category $dataProviderCategory
     * @param \MageWorx\SeoMarkup\Helper\DataProvider\Product $dataProviderProduct
     * @param \Magento\Framework\View\Layout $layout
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\View\Page\Config $pageConfig
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \MageWorx\SeoMarkup\Helper\Category $helperCategory,
        \MageWorx\SeoMarkup\Helper\LandingPage $helperLp,
        \MageWorx\SeoMarkup\Helper\DataProvider\Category $dataProviderCategory,
        \MageWorx\SeoMarkup\Helper\DataProvider\Product $dataProviderProduct,
        \Magento\Framework\View\Layout $layout,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\View\Page\Config $pageConfig
    ) {
        parent::__construct($registry, $helperCategory, $dataProviderCategory, $dataProviderProduct, $layout, $urlBuilder, $pageConfig);
        $this->helperLp     = $helperLp;
        $this->storeManager = $storeManager;
    }

    /**
     * @return string
     */
    public function getMarkupHtml()
    {
        $html = '';

        if (!$this->helperLp->isRsEnabled()) {
            return $html;
        }

        $landingpage = $this->registry->registry('mageworx_landingpagespro_landingpage');
        if (!is_object($landingpage)) {
            return false;
        }

        if ($this->helperLp->isUseLandingPageRobotsRestriction() && $this->isNoindexPage()) {
            return $html;
        }

        $landingpageJsonData = $this->getJsonLandingPageData($landingpage);
        $landingpageJson     = $landingpageJsonData ? json_encode($landingpageJsonData) : '';

        if ($landingpageJsonData) {
            $html .= '<script type="application/ld+json">' . $landingpageJson . '</script>';
        }

        return $html;
    }

    /**
     * @param $landingpage
     * @return array
     */
    protected function getJsonLandingPageData($landingpage)
    {
        $productCollection = $this->getProductCollection();

        $data = [];

        if ($productCollection) {
            $data['@context']                      = 'http://schema.org';
            $data['@type']                         = 'WebPage';
            $data['url']                           = $this->urlBuilder->getCurrentUrl();
            $data['mainEntity']                    = [];
            $data['mainEntity']['@context']        = 'http://schema.org';
            $data['mainEntity']['@type']           = 'OfferCatalog';
            $data['mainEntity']['name']            = $landingpage->getHeader($this->getStoreId());
            $data['mainEntity']['url']             = $this->urlBuilder->getCurrentUrl();
            $data['mainEntity']['numberOfItems']   = count($productCollection->getItems());
            $data['mainEntity']['itemListElement'] = [];

            if ($this->helperLp->isUseOfferForLandingPageProducts()) {
                foreach ($productCollection as $product) {
                    $data['mainEntity']['itemListElement'][] = $this->getProductData($product);
                }
            }
        }

        return $data;
    }


    /**
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|null
     */
    protected function getProductCollection()
    {
        $productList = $this->layout->getBlock('mageworx_landingpage.products.list');

        if (is_object($productList) && ($productList instanceof \Magento\Catalog\Block\Product\ListProduct)) {
            return $productList->getLoadedProductCollection();
        }

        return parent::getProductCollection();
    }

    /**
     * @return int
     */
    protected function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }
}
