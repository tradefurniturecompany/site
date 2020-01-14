<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\Observer;

use Magento\Framework\View\Page\Config;
use MageWorx\SeoBase\Model\CanonicalFactory as CanonicalFactory;
use MageWorx\SeoBase\Helper\Data as HelperData;

/**
 * Observer class for canonical URL
 */
class Canonical implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \MageWorx\SeoBase\Model\CanonicalFactory
     */
    protected $canonicalFactory;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var \MageWorx\SeoBase\Helper\Data
     */
    protected $helperData;

    /**
     * Canonical constructor.
     * @param Config $pageConfig
     * @param CanonicalFactory $canonicalFactory
     * @param HelperData $helperData
     */
    public function __construct(
        Config    $pageConfig,
        CanonicalFactory $canonicalFactory,
        HelperData $helperData
    ) {
        $this->pageConfig = $pageConfig;
        $this->canonicalFactory = $canonicalFactory;
        $this->helperData = $helperData;
    }

    /**
     * Set canonical URL to page config
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        if ($this->helperData->isDisableCanonicalByRobots()
            && stripos($this->pageConfig->getRobots(), 'noindex') !== false
        ) {
            $this->pageConfig->getAssetCollection()->remove('canonical');
            return;
        }

        $fullActionName = $observer->getFullActionName();
        $arguments      = ['layout' => $observer->getLayout(), 'fullActionName' => $fullActionName];
        $canonicalModel = $this->canonicalFactory->create($fullActionName, $arguments);
        $canonicalUrl   = $canonicalModel->getCanonicalUrl();

        if ($canonicalUrl) {
            $this->pageConfig->addRemotePageAsset(
                $canonicalUrl,
                'canonical',
                ['attributes' => ['rel' => 'canonical']]
            );
        }
    }
}
