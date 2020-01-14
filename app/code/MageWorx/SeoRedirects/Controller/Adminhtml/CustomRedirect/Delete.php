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

class Delete extends CustomRedirectController
{
    /**
     * @var CustomRedirectHelper
     */
    protected $customRedirectHelper;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Delete constructor.
     *
     * @param Registry $registry
     * @param PageFactory $resultPageFactory
     * @param CustomRedirectRepository $customRedirectRepository
     * @param CustomRedirectHelper $customRedirectHelper
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
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        /** @var \MageWorx\SeoRedirects\Model\Redirect\CustomRedirect $redirect */
        $redirect = $this->customRedirectHelper->initRedirect();

        if ($redirect->getId()) {
            try {
                $this->customRedirectRepository->delete($redirect);

                $this->messageManager->addSuccessMessage(__("The Custom Redirect has been deleted."));
                $this->_eventManager->dispatch(
                    'adminhtml_mageworx_seoredirects_customredirect_on_delete',
                    ['status' => 'success', 'id' => $redirect->getId()]
                );
                $resultRedirect->setPath('mageworx_seoredirects/*/');
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_mageworx_seoredirects_customredirect_on_delete',
                    ['status' => 'fail', 'id' => $redirect->getId()]
                );
                $this->messageManager->addErrorMessage($e->getMessage());
                $resultRedirect->setPath('mageworx_seoredirects/*/edit', ['id' => $redirect->getId()]);

                return $resultRedirect;
            }

            return $resultRedirect;
        }
        $this->messageManager->addErrorMessage(__('Custom Redirect is not found.'));
        $resultRedirect->setPath('mageworx_seoredirects/*/');

        return $resultRedirect;
    }
}
