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
 * Observer class for page crosslinks
 */
class AddCrosslinksToPageObserver implements \Magento\Framework\Event\ObserverInterface
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
        //cms_page_load_after
        $page = $observer->getObject();

        if ($this->_out($page)) {
            return;
        }

        $html            = $page->getContent();

        // check if crosslinks already exist
        if (strpos($html, $this->helperData->getLinkClass()) !== false) {
            return;
        }

        $maxReplaceCount = $this->helperData->getReplacemenetCountForCmsPage();

        $pairWidget       = array();
        $htmlWidgetCroped = $this->filter->replace($html, $pairWidget);

        $crosslink = $this->crosslinkFactory->create();
        $htmlModifyWidgetCroped = $crosslink->replace('cms_page', $htmlWidgetCroped, $maxReplaceCount);

        if ($htmlModifyWidgetCroped) {
            $htmlModify = str_replace(array_keys($pairWidget), array_values($pairWidget), $htmlModifyWidgetCroped);
            $page->setContent($htmlModify);
        }
    }

    /**
     * Check if go out
     *
     * @param $page
     * @return boolean
     */
    protected function _out($page)
    {
        if (!$this->helperData->isEnabled()) {
            return true;
        }

        if ($this->helperData->getReplacemenetCountForCmsPage() == 0) {
            return true;
        }

        if (!is_object($page)) {
            return true;
        }

        if (!in_array($this->request->getFullActionName(), $this->_getAvailableActions())) {
            return true;
        }

        if ($page->getId() != $this->request->getParam('page_id')) {
            return true;
        }

        if (!$page->getUseInCrosslinking()) {
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
        return array('cms_page_view');
    }
}
