<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
namespace MageWorx\SeoExtended\Model\MetaUpdater;

use Magento\Framework\View\Page\Config as PageConfig;
use MageWorx\SeoExtended\Helper\Data as HelperData;

class Category extends \MageWorx\SeoExtended\Model\MetaUpdater
{
    const DEFAULT_LIST_SEPARATOR = ', ';
    const DEFAULT_PAIR_SEPARATOR = ': ';

    /**
     * @var string
     */
    protected $_pairSeparator;

    /**
     * @var string
     */
    protected $_listSeparator;

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
        HelperData $helperData,
        PageConfig $pageConfig,
        \MageWorx\SeoExtended\Model\PageNumFactory $pageNumFactory,
        \MageWorx\SeoExtended\Model\FiltersConvertorFactory $filtersConvertorFactory,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageWorx\SeoExtended\Helper\SeoFilterProvider $seoFilterProvider,
        \Magento\Framework\App\RequestInterface $request
    ) {
        parent::__construct($helperData, $pageConfig, $coreRegistry, $storeManager, $request);
        $this->pageNumFactory = $pageNumFactory;
        $this->filtersConvertorFactory = $filtersConvertorFactory;
        $this->filterManager = $filterManager;
        $this->seoFilterProvider = $seoFilterProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function update($onlyFilterReplace = false)
    {
        $this->_pairSeparator = $this->_getPairSeparator();
        $this->_listSeparator = $this->_getListSeparator();

        if ($onlyFilterReplace) {
            $this->updateTitleBySeoFilter();
        } elseif ($this->helperData->isAddLayeredFiltersToMetaTitle()) {
            $this->updateTitle(false);
        }

        if ($onlyFilterReplace) {
            $this->updateMetaDescriptionBySeoFilter();
        } elseif ($this->helperData->isAddLayeredFiltersToMetaDescription()) {
            $this->updateMetaDescription(false);
        }

        if ($onlyFilterReplace) {
            $this->updateMetaKeywordsBySeoFilter();
        } elseif ($this->helperData->isAddLayeredFiltersToMetaKeywords()) {
            $this->updateMetaKeywords(false);
        }

        return true;
    }

    /**
     * @param string $title
     * @param bool $stopLnFilters
     * @return string
     */
    protected function updateTitle($stopLnFilters)
    {
        if (!$stopLnFilters) {
            $this->addLayeredFiltersToMetaTitle('catalog_category_view');
        }
        $this->addPageNumToTitle($this->pageConfig->getTitle());
    }

    /**
     * @param bool $stopLnFilters
     * @return string
     */
    protected function updateMetaDescription($stopLnFilters)
    {
        if (!$stopLnFilters) {
            $this->addLayeredFiltersToMetaDescription('catalog_category_view');
        }
        $this->addPageNumToMetaDescription($this->pageConfig->getDescription());
    }

    /**
     * @param bool $stopLnFilters
     * @return string
     */
    protected function updateMetaKeywords($stopLnFilters)
    {
        if (!$stopLnFilters) {
            $this->addLayeredFiltersToMetaKeywords('catalog_category_view');
        }
        $this->addPageNumToMetaKeywords($this->pageConfig->getKeywords());
    }

    /**
     * @return string
     */
    protected function _getPairSeparator()
    {
        return self::DEFAULT_PAIR_SEPARATOR;
    }

    /**
     * @return string
     */
    protected function _getListSeparator()
    {
        return self::DEFAULT_LIST_SEPARATOR;
    }

    //// From original observer:

    /**
     * @return \Magento\Catalog\Model\Category
     */
    protected function getCurrentCategory()
    {
        return $this->coreRegistry->registry('current_category');
    }

    /**
     * @return void
     */
    protected function addLayeredFiltersToMetaTitle()
    {
        $filtersString = $this->getStringByFilters();
        if ($filtersString) {
            $this->pageConfig->getTitle()->set(
                $this->pageConfig->getTitle()->getShortHeading() . ' | ' . $filtersString
            );
        }
    }

    /**
     * @return void
     */
    protected function addLayeredFiltersToMetaDescription()
    {
        $filtersString = $this->getStringByFilters();
        if ($filtersString) {
            $this->pageConfig->setDescription($this->pageConfig->getDescription() . ' | ' . $filtersString);
        }
    }

    /**
     * @return void
     */
    protected function addLayeredFiltersToMetaKeywords()
    {
        $filtersString = $this->getStringByFilters();
        if ($filtersString) {
            $this->pageConfig->setKeywords($this->pageConfig->getKeywords() . ', ' . $filtersString);
        }
    }

    /**
     * @return string
     */
    protected function getStringByFilters()
    {
        if ($this->filtersString === null) {
            /**
             * @var \MageWorx\SeoExtended\Model\FiltersConvertorInterface
             */
            $layeredFiltersGetterModel = $this->filtersConvertorFactory->create($this->request->getFullActionName());
            $this->filtersString = $layeredFiltersGetterModel ? $layeredFiltersGetterModel->getStringByFilters() : '';
        }

        return $this->stripTags($this->filtersString);
    }

    /**
     * Wrapper for standard strip_tags() function with extra functionality for html entities
     *
     * @param string $data
     * @param string|null $allowableTags
     * @param bool $allowHtmlEntities
     * @return string
     */
    protected function stripTags($data, $allowableTags = null, $allowHtmlEntities = false)
    {
        return $this->filterManager->stripTags(
            $data,
            [
                'allowableTags' => $allowableTags,
                'escape' => $allowHtmlEntities
            ]
        );
    }

    /**
     *
     * @param \Magento\Framework\View\Page\Title $title
     * @return void
     */
    protected function addPageNumToTitle(\Magento\Framework\View\Page\Title $title)
    {
        $pageNum = $this->getCurrentPageNum();
        $pageStringPart = __('Page') . ' ' .  $pageNum;

        if (!$pageNum) {
            return;
        }

        if ($this->helperData->isAddPageNumToBeginningMetaTitle()) {
            $title->set($pageStringPart . ' | ' . $title->getShortHeading());
        } elseif ($this->helperData->isAddPageNumToEndMetaTitle()) {
            $title->set($title->getShortHeading() . ' | ' . $pageStringPart);
        }
    }

    /**
     * @return void
     */
    protected function addPageNumToMetaDescription()
    {
        $pageNum = $this->getCurrentPageNum();
        if (!$pageNum) {
            return;
        }

        $pageStringPart = __('Page') . ' ' .  $pageNum;

        if ($this->helperData->isAddPageNumToBeginningMetaDescription()) {
            $this->pageConfig->setDescription($pageStringPart . ' | ' . $this->pageConfig->getDescription());
        } elseif ($this->helperData->isAddPageNumToEndMetaDescription()) {
            $this->pageConfig->setDescription($this->pageConfig->getDescription() . ' | ' . $pageStringPart);
        }
    }

    /**
     * @return void
     */
    protected function addPageNumToMetaKeywords()
    {
        $pageNum = $this->getCurrentPageNum();
        if (!$pageNum) {
            return;
        }

        $pageStringPart = __('Page') . ' ' .  $pageNum;

        if ($this->helperData->isAddPageNumToBeginningMetaKeywords()) {
            $this->pageConfig->setKeywords($pageStringPart . ', ' . $this->pageConfig->getKeywords());
        } elseif ($this->helperData->isAddPageNumToEndMetaKeywords()) {
            $this->pageConfig->setKeywords($this->pageConfig->getKeywords() . ', ' . $pageStringPart);
        }
    }

    /**
     * @return bool
     */
    protected function updateTitleBySeoFilter()
    {
        $category = $this->getCurrentCategory();
        $storeId  = $this->storeManager->getStore()->getId();

        $seoForFilterModel = $this->seoFilterProvider->getSeoFilter($category, $storeId);

        if (!$seoForFilterModel) {
            return false;
        }

        if (!trim($seoForFilterModel->getMetaTitle())) {
            return false;
        }

        $this->getCurrentCategory()->setMetaTitle(trim($seoForFilterModel->getMetaTitle()));
        $this->pageConfig->getTitle()->set(trim($seoForFilterModel->getMetaTitle()));
        return true;
    }


    /**
     * @return bool
     */
    protected function updateMetaDescriptionBySeoFilter()
    {
        $category = $this->getCurrentCategory();
        $storeId  = $this->storeManager->getStore()->getId();

        $seoForFilterModel = $this->seoFilterProvider->getSeoFilter($category, $storeId);

        if (!$seoForFilterModel) {
            return false;
        }

        if (!trim($seoForFilterModel->getMetaDescription())) {
            return false;
        }

        $this->getCurrentCategory()->setMetaDescription(trim($seoForFilterModel->getMetaDescription()));
        $this->pageConfig->setDescription(trim($seoForFilterModel->getMetaDescription()));

        return true;
    }

    /**
     * @return bool
     */
    protected function updateMetaKeywordsBySeoFilter()
    {
        $category = $this->getCurrentCategory();
        $storeId  = $this->storeManager->getStore()->getId();

        $seoForFilterModel = $this->seoFilterProvider->getSeoFilter($category, $storeId);

        if (!$seoForFilterModel) {
            return false;
        }

        if (!trim($seoForFilterModel->getMetaKeywords())) {
            return false;
        }

        $this->getCurrentCategory()->setMetaKeywords(trim($seoForFilterModel->getMetaKeywords()));
        $this->pageConfig->setKeywords(trim($seoForFilterModel->getMetaKeywords()));

        return true;
    }

    /**
     *
     * @param string $fullActionName
     * @return int
     */
    protected function getCurrentPageNum()
    {
        if ($this->currentPageNum === null) {
            /**
             * @var \MageWorx\SeoExtended\Model\PageNumInterface
             */
            $pageNumModel = $this->pageNumFactory->create('catalog_category_view');
            $this->currentPageNum = $pageNumModel ? $pageNumModel->getCurrentPageNum() : false;
        }
        return $this->currentPageNum;
    }
}
