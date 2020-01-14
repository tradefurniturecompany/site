<?php
/**
 * Copyright © 2019 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoMarkup\Helper\Json;

class Category
{
    /**
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \MageWorx\SeoMarkup\Helper\Category
     */
    protected $helperCategory;

    /**
     * @var \MageWorx\SeoMarkup\Helper\DataProvider\Category
     */
    protected $helperDataProvider;

    /**
     * @var \MageWorx\SeoMarkup\Helper\DataProvider\Product
     */
    protected $helperProductDataProvider;

    /**
     * @var \Magento\Framework\View\Layout
     */
    protected $layout;

    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     *
     * @param \Magento\Framework\Registry $registry
     * @param \MageWorx\SeoMarkup\Helper\Category $helperCategory
     * @param \MageWorx\SeoMarkup\Helper\DataProvider\Category $dataProviderCategory
     * @param \MageWorx\SeoMarkup\Helper\DataProvider\Product $dataProviderProduct
     * @param \Magento\Framework\View\Layout $layout
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\View\Page\Config $pageConfig
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \MageWorx\SeoMarkup\Helper\Category $helperCategory,
        \MageWorx\SeoMarkup\Helper\DataProvider\Category $dataProviderCategory,
        \MageWorx\SeoMarkup\Helper\DataProvider\Product $dataProviderProduct,
        \Magento\Framework\View\Layout $layout,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\View\Page\Config $pageConfig
    ) {
        $this->registry                   = $registry;
        $this->helperCategory             = $helperCategory;
        $this->helperDataProvider         = $dataProviderCategory;
        $this->helperProductDataProvider  = $dataProviderProduct;
        $this->layout                     = $layout;
        $this->urlBuilder                 = $urlBuilder;
        $this->pageConfig                 = $pageConfig;
    }

    /**
     * @return string
     */
    public function getMarkupHtml()
    {
        $html = '';
        $categoryJsonData = [];

        $category = $this->registry->registry('current_category');
        if (!is_object($category)) {
            return false;
        }

        if ($this->isContentMode($category)) {
            return $html;
        }

        if ($this->helperCategory->isUseCategoryRobotsRestriction() && $this->isNoindexPage()) {
            return $html;
        }

        if ($this->helperCategory->isRsEnabled()) {
            $categoryJsonData = $this->getJsonCategoryData($category);
        }

        if ($this->helperCategory->isGaEnabled()) {
            $categoryJsonData = array_merge($categoryJsonData, $this->getGoogleAssistantJsonData());
        }

        $categoryJson = !empty($categoryJsonData) ? json_encode($categoryJsonData) : '';

        if ($categoryJsonData) {
            $html .= '<script type="application/ld+json">' . $categoryJson . '</script>';
        }

        return $html;
    }

    /**
     * Check if category display mode is "Static Block Only"
     * For anchor category Static Block Only mode not allowed
     *
     * @return bool
     */
    protected function isContentMode($category)
    {
        $result = false;
        if ($category->getDisplayMode() == \Magento\Catalog\Model\Category::DM_PAGE) {
            $result = true;
            if ($category->getIsAnchor()) {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * @return array|bool
     */
    protected function getJsonCategoryData($category)
    {
        $productCollection = $this->getProductCollection();

        $data = [];

        if ($productCollection) {
            $data['@context'] = 'http://schema.org';
            $data['@type'] = 'WebPage';
            $data['url'] = $this->urlBuilder->getCurrentUrl();
            $data['mainEntity'] = [];
            $data['mainEntity']['@context'] = 'http://schema.org';
            $data['mainEntity']['@type'] = 'OfferCatalog';
            $data['mainEntity']['name'] = $category->getName();
            $data['mainEntity']['url'] = $this->urlBuilder->getCurrentUrl();
            $data['mainEntity']['numberOfItems'] = count($productCollection->getItems());
            $data['mainEntity']['itemListElement'] = [];

            if ($this->helperCategory->isUseOfferForCategoryProducts()){
                foreach ($productCollection as $product) {
                    $data['mainEntity']['itemListElement'][] = $this->getProductData($product);
                }
            }

        }
        return $data;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    protected function getProductData($product)
    {
        $data = [];
        $data['@type']    = "Product";
        $data['url']      = $product->getUrlModel()->getUrl($product, ['_ignore_category' => true]);
        $data['name']     = $product->getName();
        ///
        //$data['image'] = $this->helperProductDataProvider->getProductImage($product)->getImageUrl();
        ///
        if ($this->helperCategory->isUseOfferForCategoryProducts()) {
            $offerData        = $this->getOfferData($product);
            if (!empty($offerData['price'])) {
                $data['offers'] = $offerData;
            }
        }

        return $data;
    }

    /**
     * @param \Magento\Catalog\Model\Product
     * @return array
     */
    protected function getOfferData($product)
    {
        $data          = [];
        $data['@type'] = \MageWorx\SeoMarkup\Block\Head\Json\Product::OFFER;
        $data['price'] = $product->getFinalPrice();
        $data['priceCurrency'] = $this->helperProductDataProvider->getCurrentCurrencyCode();


        if ($this->helperProductDataProvider->getAvailability($product)) {
            $data['availability'] = \MageWorx\SeoMarkup\Block\Head\Json\Product::IN_STOCK;
        } else {
            $data['availability'] = \MageWorx\SeoMarkup\Block\Head\Json\Product::OUT_OF_STOCK;
        }

        $condition = $this->helperProductDataProvider->getConditionValue($product);
        if ($condition) {
            $data['itemCondition'] = $condition;
        }

        return $data;
    }

    /**
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|null
     */
    protected function getProductCollection()
    {
        $productList = $this->layout->getBlock('category.products.list');

        if (is_object($productList) && ($productList instanceof \Magento\Catalog\Block\Product\ListProduct)) {
            return $productList->getLoadedProductCollection();
        }

        /** @var \Magento\Theme\Block\Html\Pager $pager */
        $pager = $this->layout->getBlock('product_list_toolbar_pager');
        if (!is_object($pager)) {
            $pager = $this->getPagerFromToolbar();
        } elseif (!$pager->getCollection()) {
            $pager = $this->getPagerFromToolbar();
        }

        if (!is_object($pager)) {
            return null;
        }

        return $pager->getCollection();
    }

    /**
     *
     * @return \Magento\Catalog\Block\Product\ListProduct|null
     */
    protected function getPagerFromToolbar()
    {
        $toolbar = $this->layout->getBlock('product_list_toolbar');
        if (is_object($toolbar)) {
            $pager = $toolbar->getChild('product_list_toolbar_pager');
        }
        return !empty($pager) ? $pager : null;
    }

    /**
     * @return bool
     */
    protected function isNoindexPage()
    {
        $robots = $this->pageConfig->getRobots();

        if ($robots && stripos($robots, 'noindex') !== false) {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    protected function getGoogleAssistantJsonData()
    {
        $data['@context']         = 'http://schema.org/';
        $data['@type']            = 'WebPage';
        $speakable                = [];
        $speakable['@type']       = 'SpeakableSpecification';
        $speakable['cssSelector'] = explode(',', $this->helperCategory->getGaCssSelectors());
        $speakable['xpath']       = ['/html/head/title'];
        $data['speakable']        = $speakable;
        return $data;
    }
}
