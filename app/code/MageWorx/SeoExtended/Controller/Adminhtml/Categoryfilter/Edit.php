<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Controller\Adminhtml\Categoryfilter;

use MageWorx\SeoExtended\Controller\Adminhtml\Categoryfilter as  CategoryfilterController;

class Edit extends CategoryfilterController
{
    /**
     * backend session
     *
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * @var CategoryFilterHelper
     */
    protected $categoryFilterHelper;

    /**
     * Edit constructor.
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \MageWorx\SeoExtended\Controller\Adminhtml\CategoryFilterHelper $categoryFilterHelper
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \MageWorx\SeoExtended\Controller\Adminhtml\CategoryFilterHelper $categoryFilterHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->resultPageFactory = $pageFactory;
        $this->categoryFilterHelper = $categoryFilterHelper;
        parent::__construct($registry, $context);
    }

    public function execute()
    {
        /** @var \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface $categoryFilter */
        $categoryFilter = $this->categoryFilterHelper->initCategoryFilter();

        /** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('MageWorx_SeoExtended::categoryFilters');
        $resultPage->getConfig()->getTitle()->set((__('Category Filter')));

        $title = __('Edit Category Filter');
        $resultPage->getConfig()->getTitle()->append($title);

        $data = $this->_getSession()->getData('mageworx_seoextended_categoryfilter_data', true);
        if (!empty($data)) {
            $categoryFilter->setData($data);
        }

        if (!$categoryFilter->getId()) {
            $categoryFilter->setStoreId($this->_request->getParam('store_id'));
            $categoryFilter->setAttributeId($this->_request->getParam('attribute_id'));
        }

        return $resultPage;
    }
}
