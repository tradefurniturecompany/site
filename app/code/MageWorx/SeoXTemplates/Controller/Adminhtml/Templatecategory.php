<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use MageWorx\SeoXTemplates\Model\Template\CategoryFactory as TemplateCategoryFactory;
use Magento\Framework\Registry;

abstract class Templatecategory extends Action
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
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @param Registry $registry
     * @param TemplateCategoryFactory $templateCategoryFactory
     * @param Context $context
     */
    public function __construct(
        Registry $registry,
        TemplateCategoryFactory $templateCategoryFactory,
        Context $context
    ) {
    
        $this->coreRegistry            = $registry;
        $this->templateCategoryFactory = $templateCategoryFactory;
        parent::__construct($context);
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

    /**
     *
     * @param array $data
     * @return array
     */
    protected function filterData($data)
    {
        return $data;
    }

    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_SeoXTemplates::templatecategory');
    }
}
