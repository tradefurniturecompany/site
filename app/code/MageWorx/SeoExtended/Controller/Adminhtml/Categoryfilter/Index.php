<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Controller\Adminhtml\Categoryfilter;

use MageWorx\SeoExtended\Controller\Adminhtml\Categoryfilter as CategoryfilterController;

class Index extends CategoryfilterController
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    public function __construct(
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->resultPageFactory = $pageFactory;
        parent::__construct($registry, $context);
    }

    /**
     * Category filter list
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('SEO Category Filters'));
        $resultPage->addBreadcrumb(__('SEO Extended'), __('Extended'));
        $resultPage->addBreadcrumb(__('Category Filters'), __('Category Filters'));
        return $resultPage;
    }
}
