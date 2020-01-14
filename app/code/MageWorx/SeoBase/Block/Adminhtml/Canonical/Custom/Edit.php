<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Block\Adminhtml\Canonical\Custom;

use Magento\Backend\Block\Widget\Form\Container as FormContainer;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
use MageWorx\SeoBase\Model\CustomCanonical as CustomCanonicalModel;

class Edit extends FormContainer
{
    /**
     * Core registry
     *
     * @var Registry|null
     */
    private $coreRegistry = null;

    /**
     * Edit constructor.
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
        $this->_blockGroup = 'MageWorx_SeoBase';
        $this->_controller = 'adminhtml_canonical_custom';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Custom Canonical URL'));
        $this->buttonList->remove('delete');
    }

    /**
     * Retrieve text for header element depending on loaded template
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var \MageWorx\SeoBase\Api\Data\CustomCanonicalInterface $customCanonical */
        $customCanonical = $this->coreRegistry->registry(CustomCanonicalModel::CURRENT_CUSTOM_CANONICAL );

        if ($customCanonical && $customCanonical->getId()) {
            return __('Edit Custom Canonical URL');
        }

        return __('New Custom Canonical URL');
    }
}
