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
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use MageWorx\SeoRedirects\Controller\Adminhtml\CustomRedirectHelper;

class Edit extends CustomRedirectController
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var CustomRedirectHelper
     */
    protected $customRedirectHelper;

    /**
     *
     * @param Registry $registry
     * @param PageFactory $resultPageFactory
     * @param CustomRedirectRepository $customRedirectRepository
     * @param Context $context
     */
    public function __construct(
        Registry $registry,
        PageFactory $resultPageFactory,
        CustomRedirectRepository $customRedirectRepository,
        CustomRedirectHelper $customRedirectHelper,
        Context $context
    ) {
        $this->customRedirectHelper = $customRedirectHelper;
        $this->resultPageFactory    = $resultPageFactory;
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
        // $redirectId = $this->getRequest()->getParam('redirect_id');
        /** @var \MageWorx\SeoRedirects\Model\Redirect\CustomRedirect $redirect */
//        $redirect = $this->customRedirectRepository->getById($redirectId);
        /** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('MageWorx_SeoRedirects::customredirects');
        $resultPage->getConfig()->getTitle()->set((__('Redirect')));

//        if ($redirectId) {
//            $redirect->load($redirectId);
//            if (!$redirect->getId()) {
//                $this->messageManager->addError(__('The template no longer exists.'));
//                $resultRedirect = $this->resultRedirectFactory->create();
//                $resultRedirect->setPath(
//                    'mageworx_seoxtemplates/*/edit',
//                    [
//                        'redirect_id' => $redirect->getId(),
//                        '_current' => true
//                    ]
//                );
//                return $resultRedirect;
//            }
//        }

        $title = __('Edit Custom Redirect');
        $data  = $this->_session->getData('mageworx_seoredirect_customredirect_data', true);

        $resultPage->getConfig()->getTitle()->append($title);
        if (!empty($data)) {
            $redirect->setData($data);
        }

        return $resultPage;
    }
}
