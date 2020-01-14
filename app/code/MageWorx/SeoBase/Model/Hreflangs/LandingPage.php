<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoBase\Model\Hreflangs;

use MageWorx\SeoBase\Helper\Data as HelperData;
use MageWorx\SeoBase\Helper\Hreflangs as HelperHreflangs;
use MageWorx\SeoBase\Helper\Url as HelperUrl;
use MageWorx\SeoBase\Helper\StoreUrl as HelperStore;
use Magento\Framework\UrlInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;

class LandingPage extends \MageWorx\SeoBase\Model\Hreflangs
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
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /** @var EventManagerInterface */
    protected $eventManager;

    /**
     *
     * @var \Magento\Framework\View\Layout;
     */
    protected $layout;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var DataObject
     */
    public $dataHreflangs;

    /**
     * LandingPage constructor.
     *
     * @param \Magento\Framework\Registry $registry
     * @param HelperData $helperData
     * @param HelperUrl $helperUrl
     * @param HelperStore $helperStore
     * @param HelperHreflangs $helperHreflangs
     * @param UrlInterface $url
     * @param EventManagerInterface $eventManager
     * @param \Magento\Framework\View\Layout $layout
     * @param string $fullActionName
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        HelperData $helperData,
        HelperUrl $helperUrl,
        HelperStore $helperStore,
        HelperHreflangs $helperHreflangs,
        UrlInterface $url,
        EventManagerInterface $eventManager,
        \Magento\Framework\View\Layout $layout,
        $fullActionName
    ) {
        $this->registry           = $registry;
        $this->helperStore        = $helperStore;
        $this->url                = $url;
        $this->helperHreflangs    = $helperHreflangs;
        $this->eventManager       = $eventManager;
        $this->layout             = $layout;
        $this->dataHreflangs      = new DataObject;
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

        $landingpage = $this->getLandingPage();
        if (empty($landingpage) || !is_object($landingpage)) {
            return null;
        }

        $landingpageId = $landingpage->getId();
        $currentUrl    = $this->url->getCurrentUrl();

        if (strpos($currentUrl, '?') === false) {
            $hreflangCodes = $this->helperHreflangs->getHreflangFinalCodes('landingpage');

            if (empty($hreflangCodes)) {
                return null;
            }

            $this->dataHreflangs->setId($landingpageId);
            $this->dataHreflangs->setStores(array_keys($hreflangCodes));
            $this->dataHreflangs->setHreflangUrlsData([]);
            $this->eventManager->dispatch(
                'mageworx_seobase_add_hreflangs_to_landingpage',
                ['object' => $this->dataHreflangs]
            );

            $hreflangUrlsData = $this->dataHreflangs->getHreflangUrlsData();
            if (empty($hreflangUrlsData)) {
                return null;
            }

            $hreflangUrls = [];
            foreach ($hreflangUrlsData as $store => $altUrl) {
                $hreflang = $hreflangCodes[$store];
                $hreflangUrls[$hreflang] = $altUrl;
            }
        }

        return (!empty($hreflangUrls)) ? $hreflangUrls : null;
    }

    /**
     * @return mixed
     */
    protected function getLandingPage()
    {
        $landingpage = $this->registry->registry('mageworx_landingpagespro_landingpage');
        return $landingpage;
    }
}
