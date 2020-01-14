<?php
/**
 * Copyright © 2019 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoMarkup\Block\Head\SocialMarkup;

abstract class Page extends \MageWorx\SeoMarkup\Block\Head\SocialMarkup
{
    /**
     * @var \MageWorx\SeoMarkup\Helper\Page
     */
    protected $helperPage;

    /**
     *
     * @return string
     */
    abstract protected function getTwImageUrl();

    /**
     *
     * @return boolean
     */
    abstract protected function isOgEnabled();


    /**
     *
     * @return boolean
     */
    abstract protected function isTwEnabled();


    /**
     *
     * @return string
     */
    abstract protected function getTwUsername();


    /**
     *
     * @return string
     */
    abstract protected function getOgType();


    /**
     *
     * @return string
     */
    abstract protected function getTwType();

    /**
     * Page constructor.
     * @param \MageWorx\SeoMarkup\Helper\Page $helperPage
     * @param \MageWorx\SeoMarkup\Helper\Website $helperWebsite
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \MageWorx\SeoMarkup\Helper\Page $helperPage,
        \MageWorx\SeoMarkup\Helper\Website $helperWebsite,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data
    ) {
        $this->helperPage         = $helperPage;
        parent::__construct($registry, $helperWebsite, $context, $data);
    }

    /**
     *
     * @return string
     */
    protected function getMarkupHtml()
    {
        $html  = '';

        if (!$this->isOgEnabled() && !$this->isTwEnabled()) {
            return $html;
        }

        if ($this->isOgEnabled()) {
            $html .= $this->getOpenGraphPageInfo();
        }

        if ($this->isTwEnabled()) {
            $html .= $this->getTwitterPageInfo();
        }

        return $html;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getOpenGraphPageInfo()
    {
        $imageData = $this->getOgImageData();

        $title       = $this->escapeHtml($this->pageConfig->getTitle()->get());
        $description = $this->escapeHtml($this->pageConfig->getDescription());
        $siteName    = $this->escapeHtml($this->helperWebsite->getName());

        list($urlRaw) = explode('?', $this->_urlBuilder->getCurrentUrl());
        $url = rtrim($urlRaw, '/');

        $html = "\n<meta property=\"og:type\" content=\"" . $this->getOgType() . "\"/>\n";
        $html .= "<meta property=\"og:title\" content=\"" . $title . "\"/>\n";
        $html .= "<meta property=\"og:description\" content=\"" . $description . "\"/>\n";
        $html .= "<meta property=\"og:url\" content=\"" . $url . "\"/>\n";
        if ($siteName) {
            $html .= "<meta property=\"og:site_name\" content=\"" . $siteName . "\"/>\n";
        }

        if (isset($imageData['url'])) {
            $html .= "<meta property=\"og:image\" content=\"" . $imageData['url'] . "\"/>\n";

            if (isset($imageData['width'])) {
                $html .= "<meta property=\"og:image:width\" content=\"" . $imageData['width'] . "\"/>\n";
                $html .= "<meta property=\"og:image:height\" content=\"" . $imageData['height'] . "\"/>\n";
            }
        }

        if ($appId = $this->helperWebsite->getFacebookAppId()) {
            $html .= "<meta property=\"fb:app_id\" content=\"" . $appId . "\"/>\n";
        }

        return $html;
    }

    /**
     *
     * @return string
     */
    protected function getTwitterPageInfo()
    {
        $twitterUsername = $this->getTwUsername();
        $imageUrl        = '';

        $title       = $this->escapeHtml($this->pageConfig->getTitle()->get());
        $description = $this->escapeHtml($this->pageConfig->getDescription());

        $html = "<meta property=\"twitter:card\" content=\"" . $this->getTwType() . "\"/>\n";
        $html .= "<meta property=\"twitter:site\" content=\"" . $twitterUsername . "\"/>\n";
        $html .= "<meta property=\"twitter:title\" content=\"" . $title . "\"/>\n";
        $html .= "<meta property=\"twitter:description\" content=\"" . $description . "\"/>\n";

        if ($imageUrl) {
            $html .= "<meta property=\"twitter:image\" content=\"" . $imageUrl . "\"/>\n";
        }

        return $html;
    }
}
