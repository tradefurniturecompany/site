<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoBase\Model\Hreflangs;

use MageWorx\SeoBase\Model\ResourceModel\Catalog\Product\HreflangsFactory;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;

class Product extends \MageWorx\SeoBase\Model\Hreflangs
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
     * @var \MageWorx\SeoBase\Model\ResourceModel\Catalog\Product\HreflangFactory
     */
    protected $hreflangFactory;

    /**
     *
     * @param \MageWorx\SeoBase\Helper\Data $helperData
     * @param \MageWorx\SeoBase\Helper\Url $helperUrl
     * @param \MageWorx\SeoBase\Helper\StoreUrl $helperStore
     * @param \MageWorx\SeoBase\Helper\HelperHreflangs $helperHreflangs
     * @param Registry $registry
     * @param UrlInterface $url
     * @param HreflangsFactory $hreflangFactory
     * @param string $fullActionName
     */
    public function __construct(
        \MageWorx\SeoBase\Helper\Data $helperData,
        \MageWorx\SeoBase\Helper\Url $helperUrl,
        \MageWorx\SeoBase\Helper\StoreUrl $helperStore,
        \MageWorx\SeoBase\Helper\Hreflangs $helperHreflangs,
        Registry $registry,
        UrlInterface $url,
        HreflangsFactory $hreflangFactory,
        $fullActionName
    ) {
        $this->registry           = $registry;
        $this->helperStore        = $helperStore;
        $this->url                = $url;
        $this->helperHreflangs    = $helperHreflangs;
        $this->hreflangFactory    = $hreflangFactory;
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

        $product = $this->registry->registry('current_product');
        if (empty($product) || !is_object($product)) {
            return null;
        }

        $productId  = $product->getId();
        $categoryId = $product->getCategoryId();
        $currentUrl = $this->url->getCurrentUrl();

        if (strpos($currentUrl, '?') !== false) {
            return null;
        }

        $hreflangCodes = $this->helperHreflangs->getHreflangFinalCodes('product');
        if (empty($hreflangCodes)) {
            return null;
        }

        $hreflangResource = $this->hreflangFactory->create();
        $hreflangUrlsData = $hreflangResource->getHreflangsData(array_keys($hreflangCodes), $productId, $categoryId);

        if (empty($hreflangUrlsData[$productId]['hreflangUrls'])) {
            return null;
        }

        $hreflangUrls = [];
        foreach ($hreflangUrlsData[$productId]['hreflangUrls'] as $store => $altUrl) {
            if ($hreflangUrlsData[$productId]['requestPath'] != null){
                $hreflang = $hreflangCodes[$store];
                $hreflangUrls[$hreflang] = $altUrl;
            }
        }

        return $hreflangUrls;
    }
}
