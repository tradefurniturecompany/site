<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCrossLinks\Block\Adminhtml\Crosslink\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic as GenericForm;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Config\Model\Config\Source\Yesno as BooleanOptions;
use MageWorx\SeoAll\Helper\LandingPage;

class Destination extends GenericForm implements TabInterface
{
    /**
     * @var Type
     */
    protected $typeOptions;

    /**
     * @var BooleanOptions
     */
    protected $booleanOptions;

    /**
     * @var LandingPage
     */
    protected $helperLp;

    /**
     * Destination constructor.
     *
     * @param LandingPage $helperLp
     * @param BooleanOptions $booleanOptions
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        LandingPage $helperLp,
        BooleanOptions $booleanOptions,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    ) {
        $this->helperLp       = $helperLp;
        $this->booleanOptions = $booleanOptions;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('crosslink_');
        $form->setFieldNameSuffix('crosslink');

        $model = $this->_coreRegistry->registry('mageworx_seocrosslinks_crosslink');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Destination'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'in_product',
            'select',
            [
                'label'    => __('Use in Product'),
                'title'    => __('Use in Product'),
                'name'     => 'in_product',
                'required' => true,
                'options'  => $this->booleanOptions->toArray()
            ]
        );

        $fieldset->addField(
            'in_category',
            'select',
            [
                'label'    => __('Use in Category'),
                'title'    => __('Use in Category'),
                'name'     => 'in_category',
                'required' => true,
                'options'  => $this->booleanOptions->toArray()
            ]
        );

        $fieldset->addField(
            'in_cms_page',
            'select',
            [
                'label'    => __('Use in CMS Page'),
                'title'    => __('Use in CMS Page'),
                'name'     => 'in_cms_page',
                'required' => true,
                'options'  => $this->booleanOptions->toArray()
            ]
        );

        if ($this->helperLp->isLandingPageEnabled()) {
            $fieldset->addField(
                'in_landingpage',
                'select',
                [
                    'label'    => __('Use in Landing Page'),
                    'title'    => __('Use in Landing Page'),
                    'name'     => 'in_landingpage',
                    'required' => true,
                    'options'  => $this->booleanOptions->toArray()
                ]
            );
        }

        $form->setValues($model->getData());
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
        return __('Destination');
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
        return !$this->_storeManager->isSingleStoreMode();
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
