<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Controller\Adminhtml\Templatecategory;

use MageWorx\SeoXTemplates\Controller\Adminhtml\Templatecategory as TemplateController;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use MageWorx\SeoXTemplates\Model\Template\CategoryFactory as TemplateCategoryFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Edit extends TemplateController
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     *
     * @param Registry $registry
     * @param PageFactory $resultPageFactory
     * @param TemplateCategoryFactory $templateCategoryFactory
     * @param Context $context
     */
    public function __construct(
        Registry $registry,
        PageFactory $resultPageFactory,
        TemplateCategoryFactory $templateCategoryFactory,
        Context $context
    ) {
    
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($registry, $templateCategoryFactory, $context);
    }

    /**
     * Is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_SeoXTemplates::templatecategory');
    }

    /**
     * Edit category template
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $templateId = $this->getRequest()->getParam('template_id');
        /** @var \MageWorx\SeoXTemplates\Model\Template\Category $template */
        $template = $this->initTemplateCategory();
        /** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('MageWorx_SeoXTemplates::templatecategory');
        $resultPage->getConfig()->getTitle()->set((__('Template')));
        if ($templateId) {
            $template->load($templateId);
            if (!$template->getId()) {
                $this->messageManager->addError(__('The template no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath(
                    'mageworx_seoxtemplates/*/edit',
                    [
                        'template_id' => $template->getId(),
                        '_current' => true
                    ]
                );
                return $resultRedirect;
            }
        }
        $title = $template->getId() ? $template->getName() : __('New Category Template');
        $data  = $this->_session->getData('mageworx_seoxtemplates_template_data', true);

        $resultPage->getConfig()->getTitle()->append($title);
        if (!empty($data)) {
            $template->setData($data);
        }
        return $resultPage;
    }
}
