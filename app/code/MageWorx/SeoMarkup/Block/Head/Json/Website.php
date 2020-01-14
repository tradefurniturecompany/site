<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoMarkup\Block\Head\Json;

class Website extends \MageWorx\SeoMarkup\Block\Head\Json
{

    /**
     *
     * @var \MageWorx\SeoMarkup\Helper\Website
     */
    protected $helperWebsite;

    /**
     *
     * @param \MageWorx\SeoMarkup\Helper\Website $helperWebsite
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \MageWorx\SeoMarkup\Helper\Website $helperWebsite,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->helperWebsite = $helperWebsite;
        parent::__construct($context, $data);
    }

    /**
     *
     * {@inheritDoc}
     */
    protected function getMarkupHtml()
    {
        $html = '';

        if (!$this->helperWebsite->isRsEnabled()) {
            return $html;
        }

        $websiteJsonData = $this->getJsonWebSiteData();
        $websiteJson     = $websiteJsonData ? json_encode($websiteJsonData) : '';

        if ($websiteJsonData) {
            $html .= '<script type="application/ld+json">' . $websiteJson . '</script>';
        }

        return $html;
    }

    /**
     *
     * @return array
     */
    protected function getJsonWebSiteData()
    {
        $data = [];
        $data['@context']  = 'http://schema.org';
        $data['@type']     = 'WebSite';
        $data['url']       = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);

        $siteName = $this->helperWebsite->getName();
        if ($siteName) {
            $data['name'] = $siteName;
        }

        $siteAbout = $this->helperWebsite->getAboutInfo();
        if ($siteAbout) {
            $data['about'] = $siteAbout;
        }

        $potentialActionData = $this->getPotentialActionData();
        if ($potentialActionData) {
            $data['potentialAction'] = $potentialActionData;
        }

        return $data;
    }

    protected function getPotentialActionData()
    {
        if ($this->_request->getFullActionName() != 'cms_index_index') {
            return false;
        }

        if (!$this->helperWebsite->isAddWebsiteSearchAction()) {
            return false;
        }

        $storeBaseUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);

        $data = array();
        $data['@type']       = 'SearchAction';
        $data['target']      = $storeBaseUrl . 'catalogsearch/result/?q={search_term_string}';
        $data['query-input'] = 'required name=search_term_string';

        return $data;
    }
}
