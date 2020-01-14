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

class LandingPage extends \MageWorx\SeoExtended\Model\MetaUpdater
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
     * LandingPage constructor.
     *
     * @param HelperData $helperData
     * @param PageConfig $pageConfig
     * @param \MageWorx\SeoExtended\Model\PageNumFactory $pageNumFactory
     * @param \MageWorx\SeoExtended\Model\FiltersConvertorFactory $filtersConvertorFactory
     * @param \Magento\Framework\Filter\FilterManager $filterManager
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \MageWorx\SeoExtended\Helper\SeoFilterProvider $seoFilterProvider
     * @param \Magento\Framework\App\RequestInterface $request
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
        $this->pageNumFactory          = $pageNumFactory;
        $this->filtersConvertorFactory = $filtersConvertorFactory;
        $this->filterManager           = $filterManager;
        $this->seoFilterProvider       = $seoFilterProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function update($onlyFilterReplace = false)
    {
        $this->_pairSeparator = $this->_getPairSeparator();
        $this->_listSeparator = $this->_getListSeparator();

        //Title:
        if (!$onlyFilterReplace && $this->helperData->isAddLayeredFiltersToLpMetaTitle()) {
            $this->updateTitle(false);
        }

        //Meta Description:
        if (!$onlyFilterReplace && $this->helperData->isAddLayeredFiltersToLpMetaDescription()) {
            $this->updateMetaDescription(false);
        }

        //Meta Keywords:
        if (!$onlyFilterReplace && $this->helperData->isAddLayeredFiltersToLpMetaKeywords()) {
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
            $this->addLayeredFiltersToMetaTitle();
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
            $this->addLayeredFiltersToMetaDescription();
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
            $this->addLayeredFiltersToMetaKeywords();
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

    protected function getCurrentLandingPage()
    {
        return $this->coreRegistry->registry('mageworx_landingpagespro_landingpage');
    }

    /**
     *
     * @param \Magento\Framework\View\Page\Title $title
     * @param string $fullActionName
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
     *
     * @param \Magento\Framework\View\Page\Title $title
     * @param string $fullActionName
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
     * @param string $fullActionName
     * @return string
     */
    protected function getStringByFilters()
    {
        if ($this->filtersString === null) {
            /**
             * @var \MageWorx\SeoExtended\Model\FiltersConvertorInterface
             */
            $layeredFiltersGetterModel = $this->filtersConvertorFactory->create($this->request->getFullActionName());
            $this->filtersString       = $layeredFiltersGetterModel ? $layeredFiltersGetterModel->getStringByFilters(
            ) : '';
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
                'escape'        => $allowHtmlEntities
            ]
        );
    }

    /**
     *
     * @param \Magento\Framework\View\Page\Title $title
     * @param string $fullActionName
     * @return void
     */
    protected function addPageNumToTitle(\Magento\Framework\View\Page\Title $title)
    {
        $pageNum = $this->getCurrentPageNum();
        if (!$pageNum) {
            return;
        }

        if ($string = $this->helperData->getPageNumString()) {
            $pageStringPart = preg_replace('/%p/', $pageNum, $string);
        } else {
            $pageStringPart = __('Page') . ' ' . $pageNum;
        }


        if ($this->helperData->isAddPageNumToLpBeginningMetaTitle()) {
            $title->set($pageStringPart . ' ' . $title->getShortHeading());
        } elseif ($this->helperData->isAddPageNumToLpEndMetaTitle()) {
            $title->set($title->getShortHeading() . ' ' . $pageStringPart);
        }
    }

    /**
     *
     * @param string $fullActionName
     * @return void
     */
    protected function addPageNumToMetaDescription()
    {
        $pageNum = $this->getCurrentPageNum();
        if (!$pageNum) {
            return;
        }

        if ($string = $this->helperData->getPageNumString()) {
            $pageStringPart = preg_replace('/%p/', $pageNum, $string);
        } else {
            $pageStringPart = __('Page') . ' ' . $pageNum;
        }

        if ($this->helperData->isAddPageNumToLpBeginningMetaDescription()) {
            $this->pageConfig->setDescription($pageStringPart . ' ' . $this->pageConfig->getDescription());
        } elseif ($this->helperData->isAddPageNumToLpEndMetaDescription()) {
            $this->pageConfig->setDescription($this->pageConfig->getDescription() . ' ' . $pageStringPart);
        }
    }

    /**
     *
     * @param string $fullActionName
     * @return void
     */
    protected function addPageNumToMetaKeywords()
    {
        $pageNum = $this->getCurrentPageNum();
        if (!$pageNum) {
            return;
        }

        if ($string = $this->helperData->getPageNumString()) {
            $pageStringPart = preg_replace('/%p/', $pageNum, $string);
        } else {
            $pageStringPart = __('Page') . ' ' . $pageNum;
        }

        if ($this->helperData->isAddPageNumToLpBeginningMetaKeywords()) {
            $this->pageConfig->setKeywords($pageStringPart . ' ' . $this->pageConfig->getKeywords());
        } elseif ($this->helperData->isAddPageNumToLpEndMetaKeywords()) {
            $this->pageConfig->setKeywords($this->pageConfig->getKeywords() . ' ' . $pageStringPart);
        }
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
            $pageNumModel         = $this->pageNumFactory->create('mageworx_landingpagespro_landingpage_view');
            $this->currentPageNum = $pageNumModel ? $pageNumModel->getCurrentPageNum() : false;
        }

        return $this->currentPageNum;
    }
}
