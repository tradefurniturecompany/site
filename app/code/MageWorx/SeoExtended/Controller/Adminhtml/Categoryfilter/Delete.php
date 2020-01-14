<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Controller\Adminhtml\Categoryfilter;

use MageWorx\SeoExtended\Controller\Adminhtml\Categoryfilter as  CategoryfilterController;

class Delete extends CategoryfilterController
{
    /**
     * @var CategoryFilterHelper
     */
    protected $categoryFilterHelper;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Delete constructor.
     * @param \MageWorx\SeoExtended\Api\CategoryFilterRepositoryInterface $categoryFilterRepository
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \MageWorx\SeoExtended\Controller\Adminhtml\CategoryFilterHelper $categoryFilterHelper
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \MageWorx\SeoExtended\Api\CategoryFilterRepositoryInterface $categoryFilterRepository,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \MageWorx\SeoExtended\Controller\Adminhtml\CategoryFilterHelper $categoryFilterHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->resultPageFactory = $pageFactory;
        $this->categoryFilterHelper = $categoryFilterHelper;
        $this->categoryFilterRepository = $categoryFilterRepository;
        parent::__construct($registry, $context);
    }


    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        /** @var \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface $categoryFilter */
        $categoryFilter = $this->categoryFilterHelper->initCategoryFilter();

        if ($categoryFilter) {
            $categoryFilterId = $categoryFilter->getId();

            try {
                $this->categoryFilterRepository->delete($categoryFilter);

                $this->messageManager->addSuccessMessage(__("The Category Filter has been deleted."));
                $this->_eventManager->dispatch(
                    'adminhtml_mageworx_seoextended_categoryfilter_on_delete',
                    ['status' => 'success', 'id' => $categoryFilterId]
                );
                $resultRedirect->setPath('mageworx_seoextended/*/');
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_mageworx_seoextended_categoryfilter_on_delete',
                    ['status' => 'fail', 'id' => $categoryFilterId]
                );
                $this->messageManager->addErrorMessage($e->getMessage());
                $resultRedirect->setPath('mageworx_seoextended/*/edit', ['id' => $categoryFilterId]);

                return $resultRedirect;
            }

            return $resultRedirect;
        }
        $this->messageManager->addErrorMessage(__('Category Filter not found.'));
        $resultRedirect->setPath('mageworx_seoextended/*/');

        return $resultRedirect;
    }
}
