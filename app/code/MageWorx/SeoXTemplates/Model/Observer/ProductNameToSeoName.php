<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Observer;

use MageWorx\SeoXTemplates\Helper\Data as HelperData;
use Magento\Framework\App\RequestInterface;

/**
 * Observer class for product seo name
 */
class ProductNameToSeoName implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \MageWorx\SeoXTemplates\Helper\Data
     */
    protected $helperData;

    /**
     * Request object
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     *
     * @param HelperData $helperData
     * @param RequestInterface $request
     */
    public function __construct(
        HelperData $helperData,
        RequestInterface $request
    ) {
        $this->request    = $request;
        $this->helperData = $helperData;
    }

    /**
     * Product Name to Product SEO Name for frontend
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

        $product->setName($product->getProductSeoName());
    }

    /**
     * Check if go out
     *
     * @param $product
     * @return boolean
     */
    protected function _out($product)
    {
        if (!$this->helperData->isUseProductSeoName()) {
            return true;
        }

        if (!is_object($product)) {
            return true;
        }

        if (!in_array($this->request->getFullActionName(), $this->_getAvailableActions())) {
            return true;
        }

        if ($product->getId() != $this->request->getParam('id')) {
            return true;
        }

        if (!$product->getProductSeoName()) {
            return true;
        }
        return false;
    }

    /**
     * Retrieve list of available actions
     *
     * @return array
     */
    protected function _getAvailableActions()
    {
        return ['catalog_product_view'];
    }
}
