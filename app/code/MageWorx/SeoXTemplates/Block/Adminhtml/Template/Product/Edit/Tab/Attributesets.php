<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Block\Adminhtml\Template\Product\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic as GenericForm;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use MageWorx\SeoXTemplates\Model\Template\Product\Source\Attributesets as AttributesetsOptions;

class Attributesets extends GenericForm implements TabInterface
{
    /**
     * AttributesetsOptions
     *
     * @var array
     */
    protected $attributesetsOptions;

    /**
     * @var  \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     *
     * @param AttributesetsOptions $attributesetsOptions
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        AttributesetsOptions $attributesetsOptions,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    ) {
        $this->coreRegistry          = $registry;
        $this->attributesetsOptions  = $attributesetsOptions;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \MageWorx\SeoXTemplates\Model\Template\Product $template */
        $template = $this->getProductTemplate();

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('template_');
        $form->setFieldNameSuffix('template');

        $fieldset = $form->addFieldset(
            'attributesets_fieldset',
            [
                'legend' => $this->getLegendText(),
                'class'  => 'fieldset-wide'
            ]
        );

        $fieldset->addField(
            'attributeset',
            'select',
            [
                'name'      => 'attributeset',
                'label'     => __('Attribute Set'),
                'title'     => __('Attribute Set'),
                'required'  => true,
                'options'   => $this->getAttributeSetOptions($template)
            ]
        );

        $templateData = $this->_session->getData('mageworx_seoxtemplates_template_data', true);
        if ($templateData) {
            $template->addData($templateData);
        } else {
            if (!$template->getId()) {
                $template->addData($template->getDefaultValuesForEdit());
            }
        }

        $form->addValues($template->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Retrieve attribute set option array, filtered by same type templates
     *
     * @return array
     */
    protected function getAttributeSetOptions($template)
    {
        $options                = $this->attributesetsOptions->toArray();
        $excludeAttributesetIds = $template->getAttributesetIdsAssignedForAnalogTemplate();

        if (is_array($excludeAttributesetIds)) {
            foreach ($excludeAttributesetIds as $excludeId) {
                unset($options[$excludeId]);
            }
        }

        return $options;
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Attribute Sets');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        if ($this->getProductTemplate()->isAssignForGroupItems($this->getProductTemplate()->getAssignType())) {
            return false;
        }
        return true;
    }

    /**
     *
     * @return \MageWorx\SeoXTemplates\Model\Template\Product
     */
    public function getProductTemplate()
    {
        return $this->coreRegistry->registry('mageworx_seoxtemplates_template');
    }
}
