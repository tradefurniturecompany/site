<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Block\Adminhtml\Template\Landingpage;

use Magento\Backend\Block\Widget\Form\Container as FormContainer;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

class Edit extends FormContainer
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
    
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize template edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId   = 'template_id';
        $this->_blockGroup = 'MageWorx_SeoXTemplates';
        $this->_controller = 'adminhtml_template_landingpage';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Landing Page Template'));
    }

    /**
     * Retrieve text for header element depending on loaded template
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var \MageWorx\SeoXTemplates\Model\Template\LandingPage $template */
        $temlate = $this->coreRegistry->registry('mageworx_seoxtemplates_template');
        if ($temlate && $temlate->getId()) {
            return __("Edit Landing Page Template '%1'", $this->escapeHtml($temlate->getName()));
        }
        return __('New Template');
    }
}
