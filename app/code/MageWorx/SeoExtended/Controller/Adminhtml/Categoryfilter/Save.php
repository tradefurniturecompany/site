<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoExtended\Controller\Adminhtml\Categoryfilter;

use MageWorx\SeoExtended\Controller\Adminhtml\Categoryfilter as  CategoryfilterController;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\AlreadyExistsException;

class Save extends CategoryfilterController
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    public function __construct(
        \MageWorx\SeoExtended\Controller\Adminhtml\CategoryFilterHelper $categoryFilterHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->categoryFilterHelper = $categoryFilterHelper;
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($registry, $context);
    }

    /**
     * Run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost('categoryfilter');

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $data = $this->prepareData($data);

            /** @var \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface $categoryFilter */
            $categoryFilter = $this->categoryFilterHelper->initCategoryFilter();
            $categoryFilter->setData($data);

            $this->_eventManager->dispatch(
                'mageworx_seoextended_categoryfilter_prepare_save',
                [
                    'categoryfilter' => $categoryFilter,
                    'request' => $this->getRequest()
                ]
            );

            try {
                $categoryFilter->save();

                if (empty($multisaveFlag)) {
                    $successMessage = __('The Category Filter has been saved.');
                } else {
                    $successMessage = __('The Category Filters has been saved.');
                }

                $this->messageManager->addSuccessMessage($successMessage);
                $this->_getSession()->setData('mageworx_seoextended_categoryfilter_data', false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'mageworx_seoextended/*/edit',
                        [
                            'id' => $categoryFilter->getId(),
                            '_current' => true
                        ]
                    );
                    return $resultRedirect;
                }
                $resultRedirect->setPath('mageworx_seoextended/*/');
                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (AlreadyExistsException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the category filter.')
                );
            }

            $this->_getSession()->setData('mageworx_seoextended_categoryfilter_data', $data);
            $resultRedirect->setPath(
                'mageworx_seoextended/*/create',
                [
                    'id' => $categoryFilter->getId(),
                    '_current' => true
                ]
            );
            return $resultRedirect;
        }

        $resultRedirect->setPath('mageworx_seoextended/*/');
        return $resultRedirect;
    }
}
