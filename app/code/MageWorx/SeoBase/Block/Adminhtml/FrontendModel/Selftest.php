<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoBase\Block\Adminhtml\FrontendModel;

use MageWorx\SeoAll\Helper\LandingPage as LandingPageHelper;

class Selftest extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     *
     * @var \MageWorx\SeoBase\Helper\Data
     */
    protected $helperData;

    /**
     *
     * @var \MageWorx\SeoBase\Helper\Hreflangs
     */
    protected $helperHreflangs;

    /**
     *
     * @var LandingPageHelper
     */
    protected $helperLp;

    /**
     * Selftest constructor.
     *
     * @param LandingPageHelper $helperLp
     * @param \MageWorx\SeoBase\Helper\Data $helperData
     * @param \MageWorx\SeoBase\Helper\Hreflangs $helperHreflangs
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        LandingPageHelper $helperLp,
        \MageWorx\SeoBase\Helper\Data $helperData,
        \MageWorx\SeoBase\Helper\Hreflangs $helperHreflangs,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->helperLp        = $helperLp;
        $this->helperData      = $helperData;
        $this->helperHreflangs = $helperHreflangs;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->setElement($element);

        if ($this->helperData->getHreflangScope() == \MageWorx\SeoBase\Helper\Hreflangs::SCOPE_WEBSITE) {
            return $this->getWebsiteTableHtml($this->_getSource());
        }

        return $this->getGlobalTableHtml($this->_getSource());
    }

    protected function _getSource()
    {
        $data = [];
        $allStores =$this->_storeManager->getStores();

        foreach ($allStores as $store) {
            if (!$store->getIsActive()) {
                continue;
            }

            $duplicateProduct  = false;
            $duplicateCategory = false;
            $duplicateCms      = false;

            if (empty($data[$store->getWebsiteId()])) {
                $data[$store->getWebsiteId()] = [];
            }

            $hreflangProductCodes = $this->helperHreflangs->getHreflangRawCodes('product', $store->getStoreId());

            if (!empty($hreflangProductCodes[$store->getStoreId()])) {
                $duplicateProduct = $this->markDuplicateData(
                    $data,
                    'product_hreflang_code',
                    $store->getWebsiteId(),
                    $hreflangProductCodes[$store->getStoreId()]
                );

                if ($duplicateProduct) {
                    $store->setData('product_hreflang_code_duplicate', '1');
                }
                $store->setData('product_hreflang_code', $hreflangProductCodes[$store->getStoreId()]);
            }

            $hreflangCategoryCodes = $this->helperHreflangs->getHreflangRawCodes('category', $store->getStoreId());

            if (!empty($hreflangCategoryCodes[$store->getStoreId()])) {
                $duplicateCategory = $this->markDuplicateData(
                    $data,
                    'category_hreflang_code',
                    $store->getWebsiteId(),
                    $hreflangCategoryCodes[$store->getStoreId()]
                );

                if ($duplicateCategory) {
                    $store->setData('category_hreflang_code_duplicate', '1');
                }
                $store->setData('category_hreflang_code', $hreflangCategoryCodes[$store->getStoreId()]);
            }

            $hreflangCmsCodes = $this->helperHreflangs->getHreflangRawCodes('cms', $store->getStoreId());

            if (!empty($hreflangCmsCodes[$store->getStoreId()])) {
                $duplicateCms = $this->markDuplicateData(
                    $data,
                    'cms_hreflang_code',
                    $store->getWebsiteId(),
                    $hreflangCmsCodes[$store->getStoreId()]
                );

                if ($duplicateCms) {
                    $store->setData('cms_hreflang_code_duplicate', '1');
                }
                $store->setData('cms_hreflang_code', $hreflangCmsCodes[$store->getStoreId()]);
            }

            if ($this->helperLp->isLandingPageEnabled()) {
                $hreflangCmsCodes = $this->helperHreflangs->getHreflangRawCodes('landingpage', $store->getStoreId());

                if (!empty($hreflangCmsCodes[$store->getStoreId()])) {
                    $duplicateCms = $this->markDuplicateData(
                        $data,
                        'landingpage_hreflang_code',
                        $store->getWebsiteId(),
                        $hreflangCmsCodes[$store->getStoreId()]
                    );

                    if ($duplicateCms) {
                        $store->setData('landingpage_hreflang_code_duplicate', '1');
                    }
                    $store->setData('landingpage_hreflang_code', $hreflangCmsCodes[$store->getStoreId()]);
                }
            }

            $data[$store->getWebsiteId()]['website_name']       = $store->getWebsite()->getName();
            $data[$store->getWebsiteId()][$store->getStoreId()] = $store->getData();
        }

        return $data;
    }

    /**
     * @param $data
     * @param $type
     * @param $websiteId
     * @param $code
     * @return bool
     */
    protected function markDuplicateData(&$data, $type, $websiteId, $code)
    {
        $duplicateFlag = false;
        foreach ($data as $webId => $website) {
            if ($this->helperData->getHreflangScope() == \MageWorx\SeoBase\Helper\Hreflangs::SCOPE_WEBSITE
                && $webId != $websiteId
            ) {
                continue;
            }

            if (is_array($website) && !empty($website)) {
                foreach ($website as $storeId => $store) {
                    if (!empty($store[$type]) && $store[$type] == $code) {
                        $data[$webId][$storeId][$type . '_duplicate'] = 1;
                        $duplicateFlag = true;
                    }
                }
            }
        }

        return $duplicateFlag;
    }

    protected function getGlobalTableHtml($data)
    {
        $html = '';
        $html .= '<style type="text/css">
                        .tg  {border-collapse:collapse;border-spacing:0;}
                        .tg td{padding:5px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
                        .tg th{padding:7px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
                    </style>
                   ';

        $html .= "<table class='tg'>
                    <tr>
                      <th colspan='2'>" . __('Store') . " (code/ID)</th>
                      <th colspan='4'>" . __('Hreflang Code') . "</th>
                    </tr>
                    <tr>
                      <th colspan='2'></th>
                      <th>" . __('Product') . "</th>
                      <th>" . __('Category') . "</th>
                      <th>" . __('CMS Page') . "</th>";
        if ($this->helperLp->isLandingPageEnabled()) {
            $html .= '<th>' . __('Landing Page') . '</th>';
        }
        $html .= "</tr>";

        foreach ($data as $websiteId => $websiteData) {
            if (count($websiteData) < 2) {
                continue;
            }

            $websiteHint = (stripos($websiteData['website_name'], 'website') === false) ? ' Website' : '';
            $html .= "<tr>
                        <th colspan='5'>" . $websiteData['website_name'] . "{$websiteHint}</th>
                      </tr>
                      ";

            unset($websiteData['website_name']);

            foreach ($websiteData as $storeData) {
                $productStoreHreflang  = empty($storeData['product_hreflang_code']) ? '-' : $storeData['product_hreflang_code'];
                $productDuplicateColor = empty($storeData['product_hreflang_code_duplicate']) ? '' : ' color=red';

                $categoryStoreHreflang  = empty($storeData['category_hreflang_code']) ? '-' : $storeData['category_hreflang_code'];
                $categoryDuplicateColor = empty($storeData['category_hreflang_code_duplicate']) ? '' : ' color=red';

                $cmsStoreHreflang  = empty($storeData['cms_hreflang_code']) ? '-' : $storeData['cms_hreflang_code'];
                $cmsDuplicateColor = empty($storeData['cms_hreflang_code_duplicate']) ? '' : ' color=red';

                if ($this->helperLp->isLandingPageEnabled()) {
                    $landingPageStoreHreflang  = empty($storeData['landingpage_hreflang_code']) ? '-' : $storeData['landingpage_hreflang_code'];
                    $landingPageDuplicateColor = empty($storeData['landingpage_hreflang_code_duplicate']) ? '' : ' color=red';
                }

                $html .= "<tr>
                            <td colspan='2'>" . $storeData['name'] . "</td>
                            <td><font{$productDuplicateColor}>" . $productStoreHreflang . "</font></td>
                            <td><font{$categoryDuplicateColor}>" . $categoryStoreHreflang . "</font></td>
                            <td><font{$cmsDuplicateColor}>" . $cmsStoreHreflang . "</font></td> ";
                if ($this->helperLp->isLandingPageEnabled()) {
                    $html .=  "<td><font{$landingPageDuplicateColor}>" . $landingPageStoreHreflang . "</font></td>";
                }
                $html .="</tr>";
            }
        }

        $html .= '</table><br>';

        return $html;
    }

    protected function getWebsiteTableHtml($data)
    {
        $html = '';
        $html .= '<style type="text/css">
                        .tg  {border-collapse:collapse;border-spacing:0;}
                        .tg td{padding:5px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
                        .tg th{padding:7px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
                    </style>
                   ';

        foreach ($data as $websiteId => $websiteData) {
            if (count($websiteData) < 2) {
                continue;
            }

            $websiteHint = (stripos($websiteData['website_name'], 'website') === false) ? 'Website Name: ' : '';
            $html .= "<table class='tg'>
                      <tr>
                        <th colspan='5'>{$websiteHint}" . $websiteData['website_name'] . "</th>
                      </tr>
                      <tr>
                        <th colspan='2'>" . __('Store') . " (code/ID)</th>
                        <th colspan='4'>" . __('Hreflang Code') . "</th>
                      </tr>
                      <tr>
                        <th colspan='2'></th>
                        <th>" . __('Product') . "</th>
                        <th>" . __('Category') . "</th>
                        <th>" . __('CMS Page') . "</th>";
            if ($this->helperLp->isLandingPageEnabled()) {
                $html .= '<th>' . __('Landing Page') . '</th>';
            }
            $html .= "</tr>";

            unset($websiteData['website_name']);

            foreach ($websiteData as $storeData) {
                $productStoreHreflang  = empty($storeData['product_hreflang_code']) ? '-' : $storeData['product_hreflang_code'];
                $productDuplicateColor = empty($storeData['product_hreflang_code_duplicate']) ? '' : ' color=red';

                $categoryStoreHreflang  = empty($storeData['category_hreflang_code']) ? '-' : $storeData['category_hreflang_code'];
                $categoryDuplicateColor = empty($storeData['category_hreflang_code_duplicate']) ? '' : ' color=red';

                $cmsStoreHreflang  = empty($storeData['cms_hreflang_code']) ? '-' : $storeData['cms_hreflang_code'];
                $cmsDuplicateColor = empty($storeData['cms_hreflang_code_duplicate']) ? '' : ' color=red';

                if ($this->helperLp->isLandingPageEnabled()) {
                    $landingPageStoreHreflang  = empty($storeData['landingpage_hreflang_code']) ? '-' : $storeData['landingpage_hreflang_code'];
                    $landingPageDuplicateColor = empty($storeData['landingpage_hreflang_code_duplicate']) ? '' : ' color=red';
                }

                $html .= "<tr>
                            <td colspan='2'><b>" . $storeData['name'] . "</b><br>(" . $storeData['code'] . " / " . $storeData['store_id'] . ")</td>
                            <td><font{$productDuplicateColor}>" . $productStoreHreflang . "</font></td>
                            <td><font{$categoryDuplicateColor}>" . $categoryStoreHreflang . "</font></td>
                            <td><font{$cmsDuplicateColor}>" . $cmsStoreHreflang . "</font></td>";
                if ($this->helperLp->isLandingPageEnabled()) {
                    $html .= "<td><font{$landingPageDuplicateColor}>" . $landingPageStoreHreflang . "</font></td>";
                }
                $html .= "</tr>";
            }

            $html .= '</table><br>';
        }

        return $html;
    }
}
