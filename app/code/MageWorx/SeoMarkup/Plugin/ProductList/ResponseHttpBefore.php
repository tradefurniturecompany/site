<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoMarkup\Plugin\ProductList;

use MageWorx\SeoMarkup\Helper\Category as HelperData;
use MageWorx\SeoMarkup\Helper\LandingPage as HelperDataLandingPage;
use Magento\Framework\Registry;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Layout;
use MageWorx\SeoMarkup\Helper\Json\Category as HelperJsonCategory;
use MageWorx\SeoMarkup\Helper\Json\LandingPage as HelperJsonLandingPage;

class ResponseHttpBefore
{
    /**
     * @var  HelperData
     */
    protected $helperData;

    /**
     * @var  HelperDataLandingPage
     */
    protected $helperDataLandingPage;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Request object
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @var HelperJsonCategory
     */
    protected $heperJsonCategory;

    /**
     * @var HelperJsonLandingPage
     */
    protected $heperJsonLandingPage;

    /**
     * ResponseHttpBefore constructor.
     *
     * @param HelperData $helperData
     * @param HelperDataLandingPage $helperDataLandingPage
     * @param Registry $registry
     * @param RequestInterface $request
     * @param UrlInterface $url
     * @param Layout $layout
     * @param HelperJsonCategory $helperJsonCategory
     * @param HelperJsonLandingPage $heperJsonLandingPage
     */
    public function __construct(
        HelperData $helperData,
        HelperDataLandingPage $helperDataLandingPage,
        Registry $registry,
        RequestInterface $request,
        UrlInterface $url,
        Layout $layout,
        HelperJsonCategory $helperJsonCategory,
        HelperJsonLandingPage $heperJsonLandingPage
    ) {
        $this->helperDataLandingPage = $helperDataLandingPage;
        $this->helperData            = $helperData;
        $this->registry              = $registry;
        $this->request               = $request;
        $this->url                   = $url;
        $this->layout                = $layout;
        $this->helperJsonCategory    = $helperJsonCategory;
        $this->heperJsonLandingPage  = $heperJsonLandingPage;
    }

    /**
     * Add json category data to head block - we use plugin for avoid double loading product collection
     *
     * @param \Magento\Framework\App\Response\Http $subject
     * @param string $value
     * @return array
     */
    public function beforeAppendBody($subject, $value)
    {
        if (is_callable([$subject, 'isAjax']) && $subject->isAjax()) {
            return [$value];
        }
        $fullActionName = $this->request->getFullActionName();

        if ($fullActionName !== 'catalog_category_view'
            && $fullActionName !== 'mageworx_landingpagespro_landingpage_view') {
            return [$value];
        }
        if ($fullActionName == 'mageworx_landingpagespro_landingpage_view') {
            if (!$this->helperDataLandingPage->isRsEnabled()) {
                return [$value];
            }
            $productListJson = $this->heperJsonLandingPage->getMarkupHtml();
        } else {
            if (!$this->helperData->isRsEnabled()) {
                return [$value];
            }
            $productListJson = $this->helperJsonCategory->getMarkupHtml();
        }

        if ($productListJson) {
            $value = str_ireplace('</head>', "\n" . $productListJson . '</head>', $value);
        }

        return [$value];
    }
}
