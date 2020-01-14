<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Controller\Adminhtml\Templatecategoryfilter;

use Magento\Framework\Registry;
use MageWorx\SeoXTemplates\Controller\Adminhtml\Templatecategoryfilter as TemplateCategoryController;
use MageWorx\SeoXTemplates\Model\Template\CategoryFilterFactory as TemplateCategoryFilterFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\Helper\Js as JsHelper;
use Magento\Backend\App\Action\Context;

class Save extends TemplateCategoryController
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $jsHelper;

    /**
     *
     * @param DateTime $date
     * @param JsHelper $jsHelper
     * @param Registry $registry
     * @param TemplateCategoryFilterFactory $templateCategoryFilterFactory
     * @param Context $context
     */
    public function __construct(
        DateTime $date,
        JsHelper $jsHelper,
        Registry $registry,
        TemplateCategoryFilterFactory $templateCategoryFilterFactory,
        Context $context
    ) {
    
        $this->date     = $date;
        $this->jsHelper = $jsHelper;
        parent::__construct($registry, $templateCategoryFilterFactory, $context);
    }

    /**
     * Save the category template
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost('template');

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $data = $this->filterData($data);

            if (!empty($this->getRequest()->getParam('template')['template_id'])) {
                $template = $this->initTemplateCategory($this->getRequest()->getParam('template')['template_id']);
            } else {
                $template = $this->initTemplateCategory();
            }

            $template->setData($data);

            $this->_eventManager->dispatch(
                'mageworx_seoxtemplates_template_category_prepare_save',
                [
                    'template' => $template,
                    'request'  => $this->getRequest()
                ]
            );

            try {
                $template->setDateModified($this->date->gmtDate());
                $template->save();
                $this->_getSession()->setMageworxSeoXTemplatesTemplateCategoryData(false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'mageworx_seoxtemplates/*/edit',
                        [
                            'template_id' => $template->getId(),
                            '_current' => true
                        ]
                    );
                    return $resultRedirect;
                }
                $resultRedirect->setPath('mageworx_seoxtemplates/*/');
                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the template.'));
            }

            $this->_getSession()->setMageworxSeoXTemplatesTemplateCategoryData($data);
            $resultRedirect->setPath(
                'mageworx_seoxtemplates/*/edit',
                [
                    'template_id' => $template->getId(),
                    '_current' => true
                ]
            );
            return $resultRedirect;
        }
        $resultRedirect->setPath('mageworx_seoxtemplates/*/');
        return $resultRedirect;
    }
}
