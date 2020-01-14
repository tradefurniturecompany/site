<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Model\Generator;

use Magento\Framework\ObjectManagerInterface;
use MageWorx\XmlSitemap\Helper\Data as Helper;

/**
 * {@inheritdoc}
 */
class AdditionalLinks extends AbstractGenerator
{

    public function __construct(
        Helper $helper,
        ObjectManagerInterface $objectManager

    ) {
        $this->code = 'additional_link';
        $this->name = __('Additional links');
        parent::__construct($helper, $objectManager);
    }

    /**
     * @param $storeId
     * @param $writer
     */
    public function generate($storeId, $writer)
    {
        if ($this->helper->isShowLinks($storeId)) {
            $this->storeId = $storeId;
            $this->helper->init($this->storeId);
            $this->storeBaseUrl = $writer->storeBaseUrl;
            $collection = $this->helper->getAdditionalLinkCollection();
            $this->counter = count($collection);
            $changefreq = $this->helper->getAdditionalLinkChangefreq();
            $priority = $this->helper->getAdditionalLinkPriority();

            if (count($collection)) {
                foreach ($collection as $item) {
                    $url = $this->helper->trailingSlash($this->convertAdditionalPageUrl($item->getUrl()));
                    $lastmod = $this->helper->getCurrentDate();
                    $writer->write($url, $lastmod, $changefreq, $priority);
                }
            }
            unset($collection);
        }
    }

    /**
     * Retrieve URL
     *
     * @param string $url
     * @return string
     */
    protected function convertAdditionalPageUrl($url)
    {
        if (strpos($url, '://') !== false) {
            return $url;
        }
        return $this->storeBaseUrl . ltrim($url, '/');
    }
}