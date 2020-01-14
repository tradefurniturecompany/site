<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCrossLinks\Model\Observer;

use MageWorx\SeoCrossLinks\Model\CrosslinkFactory;
use MageWorx\SeoCrossLinks\Helper\Data as HelperData;
use MageWorx\SeoCrossLinks\Model\Filter;
use Magento\Framework\App\RequestInterface;

/**
 * Observer class for product crosslinks
 */
class AddCrosslinksToLandingPageObserver implements \Magento\Framework\Event\ObserverInterface
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
     * AddCrosslinksToLandingPageObserver constructor.
     *
     * @param CrosslinkFactory $crosslinkFactory
     * @param HelperData $helperData
     * @param RequestInterface $request
     * @param Filter $filter
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
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $landingpage = $observer->getData('landingpage');
        if ($this->out($landingpage)) {
            return;
        }

        $maxReplaceCount = $this->helperData->getReplacemenetCountForLandingPage();
        $topBlock        = $landingpage->getTopBlock();
        $bottomBlock     = $landingpage->getBottomBlock();

        $glue = '...16d8939...';

        $html = $topBlock . $glue . $bottomBlock;

        // check if crosslinks already exist
        if (strpos($html, $this->helperData->getLinkClass()) !== false) {
            return;
        }

        $pairWidget = array();

        $htmlWidgetCroped = $this->filter->replace($html, $pairWidget);

        $crosslink = $this->crosslinkFactory->create();

        $htmlModifyWidgetCroped = $crosslink->replace(
            'landingpage',
            $htmlWidgetCroped,
            $maxReplaceCount,
            null,
            null,
            $landingpage->getId()
        );

        if ($htmlModifyWidgetCroped && strpos($htmlModifyWidgetCroped, $glue) !== false) {
            $htmlModify = str_replace(array_keys($pairWidget), array_values($pairWidget), $htmlModifyWidgetCroped);

            list($topBlockMod, $bottomBlockMod) = explode($glue, $htmlModify);

            if (!empty($topBlockMod) && $topBlock) {
                $landingpage->setTopBlock($topBlockMod);
            }

            if (!empty($bottomBlockMod) && $bottomBlock) {
                $landingpage->setBottomBlock($bottomBlockMod);
            }
        }
    }

    /**
     * Check if go out
     *
     * @param $landingpage
     * @return boolean
     */
    protected function out($landingpage)
    {
        if (!$this->helperData->isEnabled()) {
            return true;
        }

        if (!is_object($landingpage)) {
            return true;
        }

        if (!$landingpage->getId()) {
            return true;
        }

        if (!$landingpage->getUseInCrosslinking()) {
            return true;
        }

        if (!$landingpage->getTopBlock() && !$landingpage->getBottomBlock()) {
            return true;
        }

        if (!in_array($this->request->getFullActionName(), $this->_getAvailableActions())) {
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
        return array('mageworx_landingpagespro_landingpage_view');
    }
}
