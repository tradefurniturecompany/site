<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Controller\Adminhtml\Templatecategory;

use MageWorx\SeoXTemplates\Model\Template\CategoryFactory as TemplateCategoryFactory;

class SuggestCategories extends \Magento\Catalog\Controller\Adminhtml\Category
{
    /**
     * Template category factory
     *
     * @var templateCategoryFactory
     */
    protected $templateCategoryFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        TemplateCategoryFactory $templateCategoryFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        parent::__construct($context);
        $this->templateCategoryFactory = $templateCategoryFactory;
        $this->coreRegistry            = $registry;
        $this->resultJsonFactory       = $resultJsonFactory;
        $this->layoutFactory           = $layoutFactory;
    }

    /**
     * Category list suggestion based on already entered symbols
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $template = $this->initTemplateCategory();

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setJsonData(
            $this->layoutFactory->create()->createBlock('MageWorx\SeoXTemplates\Block\Adminhtml\Template\Category\Tree')
                ->getSuggestedCategoriesJson($this->getRequest()->getParam('label_part'))
        );
    }

    /**
     * Init
     *
     * @return \MageWorx\SeoXTemplates\Model\Template\Category
     */
    protected function initTemplateCategory($forceTemplateId = null)
    {
        $templateId = is_null($forceTemplateId) ? $this->getRequest()->getParam('template_id') : $forceTemplateId;

        $template = $this->templateCategoryFactory->create();
        if ($templateId) {
            $template->load($templateId);
        } else {
            $template->setStoreId($this->getTemplateStoreId());
            $template->setTypeId($this->getTemplateTypeId());
        }

        if (is_null($forceTemplateId)) {
            $this->coreRegistry->register('mageworx_seoxtemplates_template', $template);
        }

        return $template;
    }

    /**
     *
     * @return int|null
     */
    protected function getTemplateStoreId()
    {
        $storeId = $this->getRequest()->getParam('store_id', -1);

        if ($storeId != -1) {
            return $storeId;
        }
        return null;
    }

    /**
     *
     * @return int|null
     */
    protected function getTemplateTypeId()
    {
        $typeId = $this->getRequest()->getParam('type_id', -1);

        if ($typeId != -1) {
            return $typeId;
        }
        return null;
    }
}
