<?php
/**
 * Copyright © 2019 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoMarkup\Block\Head\SocialMarkup;

class Product extends \MageWorx\SeoMarkup\Block\Head\SocialMarkup
{
    const IN_STOCK     = 'instock';
    const OUT_OF_STOCK = 'oos';

    /**
     * @var \MageWorx\SeoMarkup\Helper\DataProvider\Product
     */
    protected $helperDataProvider;

    /**
     * @var \MageWorx\SeoMarkup\Helper\Product
     */
    protected $helperProduct;

    /**
     * Product constructor.
     * @param \MageWorx\SeoMarkup\Helper\Product $helperProduct
     * @param \MageWorx\SeoMarkup\Helper\DataProvider\Product $helperDataProvider
     * @param \MageWorx\SeoMarkup\Helper\Website $helperWebsite
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \MageWorx\SeoMarkup\Helper\Product $helperProduct,
        \MageWorx\SeoMarkup\Helper\DataProvider\Product $helperDataProvider,
        \MageWorx\SeoMarkup\Helper\Website $helperWebsite,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data
    ) {
        $this->helperProduct      = $helperProduct;
        $this->helperDataProvider = $helperDataProvider;
        parent::__construct($registry, $helperWebsite, $context, $data);
    }

    protected function getMarkupHtml()
    {
        if (!$this->helperProduct->isOgEnabled() && !$this->helperProduct->isTwEnabled()) {
            return '';
        }

        return $this->getSocialProductInfo();
    }

    protected function getSocialProductInfo()
    {
        $product = $this->registry->registry('current_product');

        if (!is_object($product)) {
            return '';
        }

        $html = '';
        $siteName     = $this->escapeHtml($this->helperWebsite->getName());
        $url          = $this->escapeHtml($this->helperDataProvider->getProductCanonicalUrl($product));
        $descr        = $this->escapeHtml($this->helperDataProvider->getDescriptionValue($product));
        $title        = $this->escapeHtml($product->getName());
        $color        = $this->escapeHtml($this->helperDataProvider->getColorValue($product));
        $categoryName = $this->escapeHtml($this->helperDataProvider->getCategoryValue($product));
        $availability = $this->getAvailability($product);

        if ($this->helperProduct->isOgEnabled()) {

            $brand = $this->helperDataProvider->getBrandValue($product);
            if (!$brand) {
                $brand = $this->helperDataProvider->getManufacturerValue($product);
            }

            $weightString = $this->helperDataProvider->getWeightValue($product);
            $weightSep    = strpos($weightString, ' ');

            if ($weightSep !== false) {
                $weightValue  = substr($weightString, 0, $weightSep);
                $weightUnits  = $this->convertWeightUnits(substr($weightString, $weightSep + 1));
            }

            $price        = $product->getFinalPrice();
            $currency     = strtoupper($this->helperDataProvider->getCurrentCurrencyCode());
            $condition    = $this->getCondition($product);

            $html .= "\n";
            $html .= "<meta property=\"og:type\" content=\"product.item\"/>\n";
            $html .= "<meta property=\"og:title\" content=\"" . $title . "\"/>\n";
            $html .= "<meta property=\"og:description\" content=\"" . $descr . "\"/>\n";
            $html .= "<meta property=\"og:url\" content=\"" . $url . "\"/>\n";

            if (!empty($price)) {
                $html .= "<meta property=\"product:price:amount\" content=\"" . $price . "\"/>\n";

                if ($currency) {
                    $html .= "<meta property=\"product:price:currency\" content=\"" . $currency . "\"/>\n";
                }
            }

            $productImage = $this->helperDataProvider->getProductImage($product);
            $imageUrl     = $productImage->getImageUrl();
            $imageWidth   = $productImage->getWidth();
            $imageHeight  = $productImage->getHeight();

            $html .= "<meta property=\"og:image\" content=\"" . $imageUrl . "\"/>\n";
            $html .= "<meta property=\"og:image:width\" content=\"" . $imageWidth . "\"/>\n";
            $html .= "<meta property=\"og:image:height\" content=\"" . $imageHeight . "\"/>\n";

            if ($appId = $this->helperWebsite->getFacebookAppId()) {
                $html .= "<meta property=\"fb:app_id\" content=\"" . $appId . "\"/>\n";
            }

            if ($retailerItemId = $this->helperDataProvider->getProductIdValue($product)) {
                $html .= "<meta property=\"product:retailer_item_id\" content=\"" . $retailerItemId . "\"/>\n";
            }

            if ($color) {
                $html .= "<meta property=\"product:color\" content=\"" . $color . "\"/>\n";
            }

            if ($brand) {
                $html .= "<meta property=\"product:brand\" content=\"" . $brand . "\"/>\n";
            }

            if ($siteName) {
                $html .= "<meta property=\"og:site_name\" content=\"" . $siteName . "\"/>\n";
            }

            if (!empty($weightValue) && !empty($weightUnits)) {
                $html .= "<meta property=\"product:weight:value\" content=\"" . $weightValue . "\"/>\n";
                $html .= "<meta property=\"product:weight:units\" content=\"" . $weightUnits . "\"/>\n";
            }

            if ($categoryName) {
                $html .= "<meta property=\"product:category\" content=\"" . $categoryName . "\"/>\n";
            }

            $html .= "<meta property=\"product:availability\" content=\"" . $availability . "\"/>\n";

            if ($condition) {
                $html .= "<meta property=\"product:condition\" content=\"" . $condition . "\"/>\n";
            }
        }

        if ($this->helperProduct->isTwEnabled()) {
            $twitterUsername = $this->helperProduct->getTwUsername();
            if ($twitterUsername) {
                $html = $html ? $html : "\n";
                $html .= "<meta property=\"twitter:site\" content=\"" . $twitterUsername . "\"/>\n";
                $html .= "<meta property=\"twitter:creator\" content=\"" . $twitterUsername . "\"/>\n";
                $html .= "<meta property=\"twitter:card\" content=\"product\"/>\n";
                $html .= "<meta property=\"twitter:title\" content=\"" . $title . "\"/>\n";
                $html .= "<meta property=\"twitter:description\" content=\"" . $descr . "\"/>\n";
                $html .= "<meta property=\"twitter:url\" content=\"" . $url . "\"/>\n";

                if (!empty($price)) {
                    $html .= "<meta property=\"twitter:label1\" content=\"Price\"/>\n";
                    $html .= "<meta property=\"twitter:data1\" content=\"" . $price . "\"/>\n";
                }

                $html .= "<meta property=\"twitter:label2\" content=\"Availability\"/>\n";
                $html .= "<meta property=\"twitter:data2\" content=\"" . $availability . "\"/>\n";
            }
        }

        return $html;
    }

    protected function getCondition($product)
    {
        $condition = $this->helperDataProvider->getConditionValue($product);
        if ($condition) {
            $ogEnum = [
                'NewCondition'         => 'new',
                'UsedCondition'        => 'used',
                'RefurbishedCondition' => 'refurbished',
                'DamagedCondition'     => 'used'
            ];
            if (!empty($ogEnum[$condition])) {
                return $ogEnum[$condition];
            }
        }
        return '';
    }

    protected function getAvailability($product)
    {
        if ($this->helperDataProvider->getAvailability($product)) {
            return self::IN_STOCK;
        }
        return self::OUT_OF_STOCK;
    }

    /**
     *
     * @param string $value
     * @return string
     */
    protected function convertWeightUnits($value)
    {
        if (strtolower($value) == 'lbs') {
            return 'lb';
        }
    }
}
