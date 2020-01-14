<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Controller\Adminhtml\CustomRedirect;

use MageWorx\SeoRedirects\Controller\Adminhtml\CustomRedirect as CustomRedirectController;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use MageWorx\SeoRedirects\Model\Redirect\CustomRedirectRepository;
use MageWorx\SeoRedirects\Model\Redirect\CustomRedirectFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use MageWorx\SeoRedirects\Controller\Adminhtml\CustomRedirectHelper;

class Create extends CustomRedirectController
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var CustomRedirectFactory;
     */
    protected $customRedirectFactory;

    /**
     * @var CustomRedirectHelper
     */
    protected $customRedirectHelper;


    public function __construct(
        Registry $registry,
        PageFactory $resultPageFactory,
        CustomRedirectRepository $customRedirectRepository,
        CustomRedirectFactory $customRedirectFactory,
        CustomRedirectHelper $customRedirectHelper,
        Context $context
    ) {
        $this->customRedirectFactory = $customRedirectFactory;
        $this->resultPageFactory     = $resultPageFactory;
        $this->customRedirectHelper  = $customRedirectHelper;
        parent::__construct($registry, $customRedirectRepository, $context);
    }

    /**
     * Is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_SeoRedirects::customredirects');
    }

    /**
     * Edit product template
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $redirect = $this->customRedirectHelper->initRedirect();

        /** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('MageWorx_SeoRedirects::customredirects');
        $resultPage->getConfig()->getTitle()->set((__('Redirect')));

        $title = __('New Custom Redirect');
        $data  = $this->_session->getData('mageworx_seoredirect_customredirect_data', true);

        $resultPage->getConfig()->getTitle()->append($title);
        if (!empty($data)) {
            $redirect->setData($data);
        }

        return $resultPage;
    }
}
