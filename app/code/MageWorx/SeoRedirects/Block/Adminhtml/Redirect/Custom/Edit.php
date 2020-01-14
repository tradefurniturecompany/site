<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Block\Adminhtml\Redirect\Custom;

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
        $this->_objectId   = 'redirect_id';
        $this->_blockGroup = 'MageWorx_SeoRedirects';
        $this->_controller = 'adminhtml_redirect_custom';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Custom Redirect'));
        $this->buttonList->remove('delete');
    }

    /**
     * Retrieve text for header element depending on loaded template
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var \MageWorx\SeoRedirects\Model\Redirect\CustomRedirect $redirect */
        $redirect = $this->coreRegistry->registry('mageworx_seoredirects_redirect');
        if ($redirect && $redirect->getId()) {
            return __("Edit Custom Redirect '%1'", $this->escapeHtml($redirect->getName()));
        }

        return __('New Redirect');
    }
}
