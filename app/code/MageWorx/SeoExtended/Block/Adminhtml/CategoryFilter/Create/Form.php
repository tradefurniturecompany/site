<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Block\Adminhtml\CategoryFilter\Create;

use Magento\Backend\Block\Widget\Form\Generic as GenericForm;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Config\Model\Config\Source\Yesno as BooleanOptions;
use MageWorx\SeoExtended\Controller\RegistryConstants;
use MageWorx\SeoAll\Model\Source\Product\Attribute as CategoryFilterOptions;

class Form extends GenericForm
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var BooleanOptions
     */
    protected $booleanOptions;

    /**
     * @var CategoryFilterOptions
     */
    protected $categoryFilterOptions;

    /**
     * Form constructor.
     * @param CategoryFilterOptions $categoryFilterOptions
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Store\Model\System\Store $systemStore,
        CategoryFilterOptions $categoryFilterOptions,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    ) {
        $this->systemStore = $systemStore;
        $this->categoryFilterOptions = $categoryFilterOptions;
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
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id'      => 'edit_form',
                    'action'  => $this->getUrl('mageworx_seoextended/*/edit'),
                    'method'  => 'post'
                ]
            ]
        );

        $form->setUseContainer(true);

        /** @var \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface */
        $categoryFilter = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CATEGORY_FILTER_CONSTANT);

        $form->setHtmlIdPrefix('categoryfilter_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('SEO Category Filter Info'),
                'class'  => 'fieldset-wide'
            ]
        );

        if ($categoryFilter->getId()) {
            $fieldset->addField(
                'id',
                'hidden',
                ['name' => 'id']
            );
        }

        $fieldset->addField(
            'attribute_id',
            'select',
            [
                'name'      => 'attribute_id',
                'label'     => __('Attribute'),
                'title'     => __('Attribute'),
                'options'   => $this->categoryFilterOptions->toArray()
            ]
        );

        if ($this->_storeManager->isSingleStoreMode()) {
            $fieldset->addField(
                'store_id',
                'hidden',
                [
                    'name'      => 'store_id',
                    'value'     => $this->_storeManager->getStore(true)->getId()
                ]
            );
        } else {
            $fieldset->addField(
                'store_id',
                'select',
                [
                    'name'     => 'store_id',
                    'label'    => __('Store View'),
                    'title'    => __('Store View'),
                    'required' => true,
                    'values'   => $this->systemStore->getStoreValuesForForm(),
                ]
            );
        }

        $categoryFilterData = $this->_session->getData('mageworx_seoextended_categoryfilter_data', true);
        if ($categoryFilterData) {
            $categoryFilter->addData($categoryFilterData);
        } else {
            if (!$categoryFilter->getId()) {
                $categoryFilter->addData($categoryFilter->getDefaultValues());
            }
        }

        $form->addValues($categoryFilter->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
