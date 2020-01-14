<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoXTemplates\Block\Adminhtml\Template\CategoryFilter\Create\Tab;

use Magento\Backend\Block\Widget\Form\Generic as GenericForm;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Store\Model\System\Store;
use MageWorx\SeoXTemplates\Model\Template\CategoryFilter\Source\Type as TemplateTypeOptions;
use MageWorx\SeoAll\Model\Source\Product\Attribute as AttributeOptions;

class Main extends GenericForm implements TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $store;

    /**
     * @var AttributeOptions
     */
    protected $attributeOptions;

    /**
     * @var TemplateTypeOptions
     */
    protected $templateTypeOptions;

    /**
     * Main constructor.
     * @param Store $store
     * @param TemplateTypeOptions $templateTypeOptions
     * @param AttributeOptions $attributeOptions
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        Store $store,
        TemplateTypeOptions $templateTypeOptions,
        AttributeOptions $attributeOptions,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    ) {
        $this->store = $store;
        $this->templateTypeOptions = $templateTypeOptions;
        $this->attributeOptions = $attributeOptions;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \MageWorx\SeoXTemplates\Model\Template\CategoryFilter $template */
        $template = $this->_coreRegistry->registry('mageworx_seoxtemplates_template');

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('template_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Category Filter Template Info'),
                'class'  => 'fieldset-wide'
            ]
        );

        $fieldset->addField(
            'is_new',
            'hidden',
            [
                'name'      => 'is_new',
            ]
        );

        $fieldset->addField(
            'type_id',
            'select',
            [
                'label'    => __('Reference'),
                'name'     => 'type_id',
                'required' => true,
                'values'   => $this->templateTypeOptions->toArray()
            ]
        );

        $options = ['' => __('-- Please Select --')] + $this->attributeOptions->toArray();
        $fieldset->addField(
            'attribute_name',
            'select',
            [
                'label'    => __('Attribute'),
                'name'     => 'attribute_id',
                'required' => true,
                'options'  => $options
            ]
        );

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'select',
                [
                    'name'     => 'store_id',
                    'label'    => __('Store View'),
                    'title'    => __('Store View'),
                    'required' => true,
                    'values'   => $this->store->getStoreValuesForForm(false, true),
                    'note'     =>__('NOTE: Template will be added to the store view level.'),
                ]
            );
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                [
                    'name' => 'store_id',
                    'value' => $this->_storeManager->getStore(true)->getId()
                ]
            );
        }

        $templateData = $this->_session->getData('mageworx_seoxtemplates_template_data', true);
        if ($templateData) {
            $template->addData($templateData);
        } else {
            if (!$template->getId()) {
                $template->addData($template->getDefaultValuesForCreate());
            }
        }

        $form->addValues($template->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Category Filter Template');
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
        return false;
    }
}
