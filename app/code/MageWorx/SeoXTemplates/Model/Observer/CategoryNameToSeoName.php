<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Observer;

use MageWorx\SeoXTemplates\Helper\Data as HelperData;
use Magento\Framework\App\RequestInterface;

/**
 * Observer class for category seo name
 */
class CategoryNameToSeoName implements \Magento\Framework\Event\ObserverInterface
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

    public function __construct(
        HelperData $helperData,
        RequestInterface $request
    ) {
        $this->helperData  = $helperData;
        $this->request     = $request;
    }

    /**
     * Product Name to Product SEO Name for frontend
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

        $category->setName($category->getCategorySeoName());
    }

    /**
     * Check if go out
     *
     * @param $category
     * @return boolean
     */
    protected function _out($category)
    {
        if (!$this->helperData->isUseCategorySeoName()) {
            return true;
        }

        if (!is_object($category)) {
            return true;
        }

        if (!in_array($this->request->getFullActionName(), $this->_getAvailableActions())) {
            return true;
        }

        if ($category->getId() != $this->request->getParam('id')) {
            return true;
        }

        if (!$category->getCategorySeoName()) {
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
        return ['catalog_category_view'];
    }
}
