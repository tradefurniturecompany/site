<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCrossLinks\Model\Observer;

use MageWorx\SeoCrossLinks\Model\CrosslinkFactory;
use MageWorx\SeoCrossLinks\Helper\Data as HelperData;
use MageWorx\SeoCrossLinks\Model\Filter;
use Magento\Framework\App\RequestInterface;

/**
 * Observer class for category crosslinks
 */
class AddCrosslinksToCategoryObserver implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * crosslink factory
     *
     * @var CrosslinkFactory
     */
    protected $crosslinkFactory;

    /**
     * @var \MageWorx\SeoCrossLinks\Helper\Data
     */
    protected $helperData;

    /**
     * Request object
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * Filter object
     *
     * @var \MageWorx\SeoCrossLinks\Model\Filter
     */
    protected $filter;

    /**
     * @param \MageWorx\SeoCrossLinks\Helper\Data $helperData
     */
    public function __construct(
        CrosslinkFactory $crosslinkFactory,
        HelperData $helperData,
        RequestInterface $request,
        Filter $filter
    ) {
        $this->crosslinkFactory = $crosslinkFactory;
        $this->helperData       = $helperData;
        $this->request          = $request;
        $this->filter           = $filter;
    }

    /**
     * Modify category description
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        //catalog_category_load_after
        $category = $observer->getData('category');

        if ($this->_out($category)) {
            return;
        }

        $html = $category->getDescription();

        // check if crosslinks already exist
        if (strpos($html, $this->helperData->getLinkClass()) !== false) {
            return;
        }

        $maxReplaceCount = $this->helperData->getReplacemenetCountForCategoryPage();

        $pairWidget       = array();
        $htmlWidgetCroped = $this->filter->replace($html, $pairWidget);

        $crosslink = $this->crosslinkFactory->create();
        $htmlModifyWidgetCroped = $crosslink->replace('category', $htmlWidgetCroped, $maxReplaceCount, null, $category->getId());

        if ($htmlModifyWidgetCroped) {
            $htmlModify = str_replace(array_keys($pairWidget), array_values($pairWidget), $htmlModifyWidgetCroped);
            $category->setDescription($htmlModify);
        }
    }

    /**
     * Check if go out
     *
     * @param $category
     * @return boolean
     */
    protected function _out($category)
    {
        if (!$this->helperData->isEnabled()) {
            return true;
        }

        if ($this->helperData->getReplacemenetCountForCategoryPage() == 0) {
            return true;
        }

        if (!is_object($category)) {
            return true;
        }

        if (!$category->getUseInCrosslinking()) {
            return true;
        }

        if (!in_array($this->request->getFullActionName(), $this->_getAvailableActions())) {
            return true;
        }

        if ($category->getId() != $this->request->getParam('id')) {
            return true;
        }
        return false;
    }

    /**
     * Retrive list of available actions
     *
     * @return array
     */
    protected function _getAvailableActions()
    {
        return array('catalog_category_view');
    }
}
