<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoMarkup\Block\Head\Json;

class Seller extends \MageWorx\SeoMarkup\Block\Head\Json
{
    /**
     *
     * @var \MageWorx\SeoMarkup\Helper\Seller
     */
    protected $helperSeller;

    /**
     *
     * @param \MageWorx\SeoMarkup\Helper\Seller $helperSeller
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \MageWorx\SeoMarkup\Helper\Seller $helperSeller,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->helperSeller = $helperSeller;
        parent::__construct($context, $data);
    }

    /**
     *
     * {@inheritDoc}
     */
    protected function getMarkupHtml()
    {
        $html = '';

        if (!$this->helperSeller->isRsEnabled()) {
            return $html;
        }

        $sellerJsonData = $this->getJsonOrganizationData();
        $sellerJson     = $sellerJsonData  ? json_encode($sellerJsonData) : '';

        if ($sellerJsonData) {
            $html .= '<script type="application/ld+json">' . $sellerJson . '</script>';
        }

        return $html;
    }

    /**
     *
     * @return array|boolean
     */
    protected function getJsonOrganizationData()
    {
        $name = $this->helperSeller->getName();
        $image = $this->getImageUrl();

        if (!$name || ! $image) { // Name and Image are required fields
            return false;
        }
        $data = [];
        $data['@context']    = 'http://schema.org';
        $data['@type']       = $this->helperSeller->getType();

        $name = $this->helperSeller->getName();
        if ($name) {
            $data['name'] = $name;
        }

        $description = $this->helperSeller->getDescription();
        if ($description) {
            $data['description'] = $description;
        }

        $phone = $this->helperSeller->getPhone();
        if ($phone) {
            $data['telephone'] = $phone;
        }

        $email = $this->helperSeller->getEmail();
        if ($email) {
            $data['email'] = $email;
        }

        $fax = $this->helperSeller->getFax();
        if ($fax) {
            $data['faxNumber'] = $fax;
        }

        $address = $this->getAddress();
        if ($address && count($address) > 1) {
            $data['address'] = $address;
        }

        $socialLinks = $this->helperSeller->getSameAsLinks();
        if (is_array($socialLinks) && !empty($socialLinks)) {
            $data['sameAs'] = [];
            $data['sameAs'][] = $socialLinks;
        }

        if ($image) {
            $data['image'] = $image;
        }

        $priceRange =  $this->helperSeller->getPriceRange();
        if ($priceRange) {
            $data['priceRange'] =  $priceRange;
        }

        $data['url'] = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);

        return $data;
    }

    /**
     *
     * @return array
     */
    protected function getAddress()
    {
        $data = [];
        $data['@type']           = 'PostalAddress';
        $data['addressLocality'] = $this->helperSeller->getLocation();
        $data['addressRegion']   = $this->helperSeller->getRegionAddress();
        $data['streetAddress']   = $this->helperSeller->getStreetAddress();
        $data['postalCode']      = $this->helperSeller->getPostCode();
        return $data;
    }

    /**
     *
     * @return string|boolean
     */
    protected function getImageUrl()
    {
        $folderName = 'seller_image';
        $storeConfig = $this->helperSeller->getImage();
        $faviconFile = $this->_storeManager->getStore()->getBaseUrl('media') . $folderName . '/' . $storeConfig;
        if (!is_null($storeConfig)) {
            return $faviconFile;
        }
        return false;
    }
}
