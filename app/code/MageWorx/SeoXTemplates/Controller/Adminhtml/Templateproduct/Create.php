<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Controller\Adminhtml\Templateproduct;

use MageWorx\SeoXTemplates\Controller\Adminhtml\Templateproduct as TemplateController;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use MageWorx\SeoXTemplates\Model\Template\ProductFactory as TemplateProductFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Create extends TemplateController
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     *
     * @param Registry $registry
     * @param PageFactory $resultPageFactory
     * @param TemplateProductFactory $templateProductFactory
     * @param Context $context
     */
    public function __construct(
        Registry $registry,
        PageFactory $resultPageFactory,
        TemplateProductFactory $templateProductFactory,
        Context $context
    ) {
    
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($registry, $templateProductFactory, $context);
    }

    /**
     * Is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_SeoXTemplates::templateproduct');
    }

    /**
     * Create product template
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $templateId = $this->getRequest()->getParam('template_id');
        /** @var \MageWorx\SeoXTemplate\Model\Template\Product $template */
        $template = $this->initTemplateProduct();
        /** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('MageWorx_SeoXTemplates::templateproduct');
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

        $title = $template->getId() ? $template->getName() : __('New Product Template');
        $data  = $this->_session->getData('mageworx_seoxtemplates_template_data', true);

        $resultPage->getConfig()->getTitle()->append($title);
        if (!empty($data)) {
            $template->setData($data);
        }
        return $resultPage;
    }
}
