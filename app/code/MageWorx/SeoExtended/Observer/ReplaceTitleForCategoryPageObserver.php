<?php
/**
 * Copyright ©  MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoExtended\Observer;

use Magento\Framework\View\Page\Config as PageConfig;
use MageWorx\SeoExtended\Helper\Data as HelperData;

/**
 * Observer class for category seo name
 */
class ReplaceTitleForCategoryPageObserver implements \Magento\Framework\Event\ObserverInterface
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
     * ReplaceCategoryNameObserver constructor.
     *
     * @param \MageWorx\SeoExtended\Model\MetaUpdaterFactory $metaUpdaterFactory
     * @param HelperData $helperData
     * @param PageConfig $pageConfig
     * @param \MageWorx\SeoExtended\Model\PageNumFactory $pageNumFactory
     * @param \MageWorx\SeoExtended\Model\FiltersConvertorFactory $filtersConvertorFactory
     * @param \Magento\Framework\Filter\FilterManager $filterManager
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \MageWorx\SeoExtended\Helper\SeoFilterProvider $seoFilterProvider
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
        $this->metaUpdaterFactory      = $metaUpdaterFactory;
        $this->helperData              = $helperData;
        $this->pageConfig              = $pageConfig;
        $this->pageNumFactory          = $pageNumFactory;
        $this->filtersConvertorFactory = $filtersConvertorFactory;
        $this->filterManager           = $filterManager;
        $this->coreRegistry            = $coreRegistry;
        $this->request                 = $request;
        $this->storeManager            = $storeManager;
        $this->seoFilterProvider       = $seoFilterProvider;
    }


    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return bool|void
     * @throws \Magento\Framework\Exception\LocalizedException
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

        $pageMainTitle = $observer->getObserver()->getLayout()->getBlock('page.main.title');
        if (!$pageMainTitle) {
            return false;
        }

        $storeId           = $this->storeManager->getStore()->getId();
        $seoForFilterModel = $this->seoFilterProvider->getSeoFilter($category, $storeId);

        if (!$seoForFilterModel) {
            return false;
        }

        if (!trim($seoForFilterModel->getCategorySeoName())) {
            return false;
        }

        $pageMainTitle->setPageTitle(trim($seoForFilterModel->getCategorySeoName()));
    }
}
