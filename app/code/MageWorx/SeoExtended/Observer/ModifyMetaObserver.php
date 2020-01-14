<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Observer;

use Magento\Framework\View\Page\Config as PageConfig;
use MageWorx\SeoExtended\Helper\Data as HelperData;

/**
 * Observer class for modify meta title, description, keywords
 */
class ModifyMetaObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \MageWorx\SeoExtended\Model\MetaUpdaterFactory
     */
    protected $metaUpdaterFactory;

    /**
     *
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     *
     * @var \MageWorx\SeoExtended\Model\PageNumFactory
     */
    protected $pageNumFactory;

    /**
     *
     * @var \MageWorx\SeoExtended\Model\LayeredFiltersFactory
     */
    protected $layeredFiltersFactory;

    /**
     *
     * @var \MageWorx\SeoExtended\Model\FiltersConvertorFactory
     */
    protected $filtersConvertorFactory;

    /**
     * Filter manager
     *
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filterManager;

    /**
     *
     * @var int|false|null
     */
    protected $currentPageNum;

    /**
     *
     * @var string|null
     */
    protected $filtersString;

    /**
     *
     * @param HelperData $helperData
     * @param PageConfig $pageConfig
     * @param \MageWorx\SeoExtended\Model\PageNumFactory $pageNumFactory
     * @param \MageWorx\SeoExtended\Model\FiltersConvertorFactory $filtersConvertorFactory
     */
    public function __construct(
        \MageWorx\SeoExtended\Model\MetaUpdaterFactory $metaUpdaterFactory,
        HelperData $helperData,
        PageConfig $pageConfig,
        \MageWorx\SeoExtended\Model\PageNumFactory $pageNumFactory,
        \MageWorx\SeoExtended\Model\FiltersConvertorFactory $filtersConvertorFactory,
        \Magento\Framework\Filter\FilterManager $filterManager
    ) {
        $this->metaUpdaterFactory = $metaUpdaterFactory;
        $this->helperData = $helperData;
        $this->pageConfig = $pageConfig;
        $this->pageNumFactory = $pageNumFactory;
        $this->filtersConvertorFactory = $filtersConvertorFactory;
        $this->filterManager = $filterManager;
    }

    /**
     * Modify meta data
     * event: layout_generate_blocks_after
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $fullActionName = $observer->getFullActionName();

        /** @var \MageWorx\SeoExtended\Model\MetaUpdaterInterface $updater */
        $updater = $this->metaUpdaterFactory->create($fullActionName);

        if (!$updater) {
            return;
        }

        return $updater->update();
    }
}
