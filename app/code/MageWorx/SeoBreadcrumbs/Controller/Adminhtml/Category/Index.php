<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBreadcrumbs\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;

class Index extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\Page
     */
    protected $resultPage;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Index constructor.
     * @param StoreManagerInterface $storeManager
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Context $context,
        PageFactory $resultPageFactory
    ) {
    
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if (!array_key_exists('store', $this->_request->getParams())) {
            return $this->_redirect('*/*/*', ['store' => $this->storeManager->getStore()->getId()]);
        }

        $this->setPageData();
        return $this->getResultPage();
    }

    /**
     * Instantiate result page object
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page
     */
    public function getResultPage()
    {
        if (is_null($this->resultPage)) {
            $this->resultPage = $this->resultPageFactory->create();
        }
        return $this->resultPage;
    }

    /**
     * Set page data
     *
     * @return $this
     */
    protected function setPageData()
    {
        $resultPage = $this->getResultPage();
        $resultPage->setActiveMenu('MageWorx_SeoBreadcrumbs::category');
        $resultPage->getConfig()->getTitle()->set((__('Breadcrumbs Priority for Categories')));
        return $this;
    }

    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_SeoBreadcrumbs::category');
    }
}
