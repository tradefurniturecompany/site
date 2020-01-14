<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Observer;

use Magento\Framework\View\Page\Config as PageConfig;
use MageWorx\SeoExtended\Helper\Data as HelperData;

class ReplaceCategoryDescriptionObserver implements \Magento\Framework\Event\ObserverInterface
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
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \MageWorx\SeoExtended\Helper\SeoFilterProvider
     */
    protected $seoFilterProvider;

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
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageWorx\SeoExtended\Helper\SeoFilterProvider $seoFilterProvider
    ) {
        $this->metaUpdaterFactory = $metaUpdaterFactory;
        $this->helperData = $helperData;
        $this->pageConfig = $pageConfig;
        $this->pageNumFactory = $pageNumFactory;
        $this->filtersConvertorFactory = $filtersConvertorFactory;
        $this->filterManager = $filterManager;
        $this->coreRegistry = $coreRegistry;
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->seoFilterProvider = $seoFilterProvider;
    }


    /**
     * Convert properties of the product that contain [category] and [categories]
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helperData->isUseSeoForCategoryFilters()) {
            return false;
        }

        $fullActionName = $observer->getFullActionName();

        if ('catalog_category_view' != $fullActionName) {
            return false;
        }

        $category = $this->coreRegistry->registry('current_category');

        if (!is_object($category)) {
            return false;
        }

        if ($this->request->getParam('id') != $category->getId()) {
            return false;
        }


        $storeId = $this->storeManager->getStore()->getId();

        $seoForFilterModel = $this->seoFilterProvider->getSeoFilter($category, $storeId);

        if (!$seoForFilterModel) {
            return false;
        }

        if (!trim($seoForFilterModel->getDescription())) {
            return false;
        }

        $category->setDescription(trim($seoForFilterModel->getDescription()));
    }
}
