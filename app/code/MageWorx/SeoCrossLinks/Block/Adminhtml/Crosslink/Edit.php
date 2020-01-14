<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCrossLinks\Block\Adminhtml\Crosslink;

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
     * Initialize crosslink edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'crosslink_id';
        $this->_blockGroup = 'MageWorx_SeoCrossLinks';
        $this->_controller = 'adminhtml_crosslink';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Crosslink'));
        $this->buttonList->remove('delete');
    }

    /**
     * Get URL for save with reduce crosslink priority
     *
     * @return string
     */
    public function getSaveWithReduceUrl()
    {
        return $this->getUrl('*/*/save/reduce_priority/1/');
    }

    /**
     * Retrieve text for header element depending on loaded crosslink
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var \MageWorx\SeoCrossLinks\Model\Crosslink $crosslink */
        $crosslink = $this->coreRegistry->registry('mageworx_seocrosslinks_crosslink');
        if ($crosslink && $crosslink->getId()) {
            return __("Edit Crosslink '%1'", $this->escapeHtml($crosslink->getKeyword()));
        }
        return __('New Crosslink');
    }
}
