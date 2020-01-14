<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Block\Adminhtml\CategoryFilter;

use Magento\Backend\Block\Widget\Form\Container as FormContainer;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

class Create extends FormContainer
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * constructor
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
     * Initialize edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'MageWorx_SeoExtended';
        $this->_controller = 'adminhtml_categoryFilter';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Continue Edit'));
        $this->buttonList->remove('delete');
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->addChild('form', 'MageWorx\SeoExtended\Block\Adminhtml\CategoryFilter\Create\Form');
        return parent::_prepareLayout();
    }
}
