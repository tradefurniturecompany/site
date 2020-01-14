<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use MageWorx\SeoXTemplates\Model\Template\LandingPageFactory as TemplateLandingPageFactory;
use Magento\Framework\Registry;

abstract class Templatelandingpage extends Action
{
    /**
     * Template Landing Page factory
     *
     * @var TemplateLandingPageFactory
     */
    protected $templateLandingPageFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * Templatelandingpage constructor.
     *
     * @param Registry $registry
     * @param TemplateLandingPageFactory $templateLandingPageFactory
     * @param Context $context
     */
    public function __construct(
        Registry $registry,
        TemplateLandingPageFactory $templateLandingPageFactory,
        Context $context
    ) {
    
        $this->coreRegistry               = $registry;
        $this->templateLandingPageFactory = $templateLandingPageFactory;
        parent::__construct($context);
    }

    /**
     * @param null $forceTemplateId
     * @return \MageWorx\SeoXTemplates\Model\Template\LandingPage
     */
    protected function initTemplateLandingPage($forceTemplateId = null)
    {
        $templateId = is_null($forceTemplateId) ? $this->getRequest()->getParam('template_id') : $forceTemplateId;

        /** @var \MageWorx\SeoXTemplates\Model\Template\LandingPage $template */
        $template = $this->templateLandingPageFactory->create();
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
        if ($data['store_id'] == 'default') {
            $data['store_id'] = 0;
            $data['use_for_default_value'] = true;
        }
        return $data;
    }

    /**
     * Is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_SeoXTemplates::templatelandingpage');
    }
}
