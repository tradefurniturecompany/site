<?php
/**
 * Copyright © 2019 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoMarkup\Block\Head;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface;

abstract class SocialMarkup extends \MageWorx\SeoMarkup\Block\Head
{
    /**
     * @var \MageWorx\SeoMarkup\Helper\Website
     */
    protected $helperWebsite;

    /**
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * SocialMarkup constructor.
     * @param \Magento\Framework\Registry $registry
     * @param \MageWorx\SeoMarkup\Helper\Website $helperWebsite
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \MageWorx\SeoMarkup\Helper\Website $helperWebsite,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data
    ) {
        $this->registry           = $registry;
        $this->helperWebsite  = $helperWebsite;
        parent::__construct($context, $data);
    }

    /**
     *
     * {@inheritDoc}
     */
    protected function _toHtml()
    {
        return $this->getMarkupHtml();
    }

    /**
     * Retrieve facebook logo
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOgImageData()
    {
        $imageData   = [];
        $folderName  = 'og_image';
        $storeConfig = $this->helperWebsite->getOgImage();
        $filePath    = $folderName . DIRECTORY_SEPARATOR . $storeConfig;
        $imageUrl    = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $filePath;

        if ($storeConfig !== '') {
            $imageData['url'] = $imageUrl;

            $mediaDir = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);

            if ($mediaDir->isFile($filePath)) {
                $absolutePath = $mediaDir->getAbsolutePath($filePath);
                $imageAttr    = getimagesize($absolutePath);

                $imageData['width']  = $imageAttr[0];
                $imageData['height'] = $imageAttr[1];
            }
        }

        return $imageData;
    }
}
