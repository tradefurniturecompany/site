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
 * Observer class for product crosslinks
 */
class AddCrosslinksToProductObserver implements \Magento\Framework\Event\ObserverInterface
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
     * Modify product short description and description
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        //catalog_product_load_after
        $product = $observer->getData('product');

        if ($this->_out($product)) {
            return;
        }

        $attributes       = $this->helperData->getProductAttributesForReplace();
        $maxReplaceCount  = $this->helperData->getReplacemenetCountForProductPage();
        $shortDescription = in_array('short_description', $attributes) ? $product->getShortDescription() : '';
        $description      = in_array('description', $attributes) ? $product->getDescription() : '';

        if (!$shortDescription && !$description) {
            return;
        }

        $glue = '...16d8939...';

        $html = $shortDescription . $glue . $description;

        // check if crosslinks already exist
        if (strpos($html, $this->helperData->getLinkClass()) !== false) {
            return;
        }

        $pairWidget       = array();

        $htmlWidgetCroped = $this->filter->replace($html, $pairWidget);

        $crosslink = $this->crosslinkFactory->create();

        $htmlModifyWidgetCroped = $crosslink->replace('product', $htmlWidgetCroped, $maxReplaceCount, $product->getSku());

        if ($htmlModifyWidgetCroped && strpos($htmlModifyWidgetCroped, $glue) !== false) {
            $htmlModify = str_replace(array_keys($pairWidget), array_values($pairWidget), $htmlModifyWidgetCroped);

            list($shortDescriptionMod, $descriptionMod) = explode($glue, $htmlModify);

            if (!empty($shortDescriptionMod) && $shortDescription) {
                $product->setShortDescription($shortDescriptionMod);
            }

            if (!empty($descriptionMod) && $description) {
                $product->setDescription($descriptionMod);
            }
        }
    }

    /**
     * Check if go out
     *
     * @param $product
     * @return boolean
     */
    protected function _out($product)
    {
        if (!$this->helperData->isEnabled()) {
            return true;
        }

        if ($this->helperData->getReplacemenetCountForProductPage() == 0) {
            return true;
        }

        if (!count($this->helperData->getProductAttributesForReplace())) {
            return true;
        }

        if (!is_object($product)) {
            return true;
        }

        if (!$product->getUseInCrosslinking()) {
            return true;
        }

        if (!in_array($this->request->getFullActionName(), $this->_getAvailableActions())) {
            return true;
        }

        if ($product->getId() != $this->request->getParam('id')) {
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
        return array('catalog_product_view');
    }
}
