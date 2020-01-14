<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Controller\Adminhtml\DpRedirect;

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
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Context $context,
        PageFactory $resultPageFactory
    ) {

        parent::__construct($context);
        $this->storeManager      = $storeManager;
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
        $resultPage->setActiveMenu('MageWorx_SeoRedirects::dpredirects');
        $resultPage->getConfig()->getTitle()->set((__('Redirects for Deleted Products')));

        return $this;
    }

    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_SeoRedirects::dpredirects');
    }
}
