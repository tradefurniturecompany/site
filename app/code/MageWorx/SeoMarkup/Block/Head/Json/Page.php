<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoMarkup\Block\Head\Json;

class Page extends \MageWorx\SeoMarkup\Block\Head\Json
{
    /**
     *
     * @var \MageWorx\SeoMarkup\Helper\Page
     */
    protected $helperPage;

    /**
     *
     * @param \MageWorx\SeoMarkup\Helper\Page $helperPage
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \MageWorx\SeoMarkup\Helper\Page $helperPage,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->helperPage          = $helperPage;
        parent::__construct($context, $data);
    }

    /**
     *
     * {@inheritDoc}
     */
    protected function getMarkupHtml()
    {
        $html            = '';
        $pageJsonData = [];

        if ($this->helperPage->isGaEnabled()) {
            $pageJsonData = $this->getGoogleAssistantJsonData();
        }

        $pageJson = !empty($pageJsonData) ? json_encode($pageJsonData) : '';

        if ($pageJson) {
            $html .= '<script type="application/ld+json">' . $pageJson . '</script>';
        }

        return $html;
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
        $speakable['cssSelector'] = explode(',', $this->helperPage->getGaCssSelectors());
        $speakable['xpath']       = ['/html/head/title'];
        $data['speakable']        = $speakable;
        return $data;
    }
}
