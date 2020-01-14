<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Block\Adminhtml\CategoryFilter\Edit;

use Magento\Backend\Block\Widget\Form\Generic as GenericForm;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Config\Model\Config\Source\Yesno as BooleanOptions;
use MageWorx\SeoExtended\Controller\RegistryConstants;

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
     * @var \MageWorx\SeoAll\Model\Source\Category
     */
    protected $categoryOptions;

    /**
     * @var \MageWorx\SeoAll\Model\Source\Product\AttributeOption
     */
    protected $attributeValueOptions;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $wysiwygConfig;

    /**
     * @var \MageWorx\SeoAll\Helper\Adapter
     */
    protected $helperAdapter;

    /**
     * @var \MageWorx\SeoAll\Model\Source\Product\Attribute
     */
    protected $attributeValue;

    /**
     * Form constructor.
     *
     * @param \MageWorx\SeoAll\Model\Source\Category $categoryOptions
     * @param \MageWorx\SeoAll\Model\Source\Product\AttributeOption $attributeValueOptions
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \MageWorx\SeoAll\Helper\Adapter $helperAdapter
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \MageWorx\SeoAll\Model\Source\Category $categoryOptions,
        \MageWorx\SeoAll\Model\Source\Product\AttributeOption $attributeValueOptions,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Store\Model\System\Store $systemStore,
        \MageWorx\SeoAll\Helper\Adapter $helperAdapter,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    ) {
        $this->wysiwygConfig = $wysiwygConfig;
        $this->categoryOptions = $categoryOptions;
        $this->attributeValueOptions = $attributeValueOptions;
        $this->systemStore = $systemStore;
        $this->helperAdapter = $helperAdapter;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface */
        $categoryFilter = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CATEGORY_FILTER_CONSTANT);

        $categoryFilterData = $this->_session->getData('mageworx_seoextended_categoryfilter_data', true);

        if ($categoryFilterData) {
            $categoryFilter->addData($categoryFilterData);
        } else {
            if (!$categoryFilter->getId()) {
                $categoryFilter->addData($categoryFilter->getDefaultValues());
            }
        }

        $data = $this->prepareDataForForm($categoryFilter);

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                ]
            ]
        );

        $form->setUseContainer(true);
        $form->setHtmlIdPrefix('categoryfilter_');
        $form->setFieldNameSuffix('categoryfilter');

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
            'attribute_name',
            'label',
            [
                'label'   => __('Attribute'),
                'value'   => $categoryFilter->getAttributeLabel()
            ]
        );

        $fieldset->addField(
            'attribute_id',
            'hidden',
            [
                'name'     => 'attribute_id',
            ]
        );

        if ($categoryFilter->getId()) {

            if ($categoryFilter->getAttributeOptionId()) {
                $values = $this->attributeValueOptions->toArray($data['attribute_id']);

                if (empty($values[$data['attribute_option_id']])) {
                    $value = __('Unknown');
                } else {
                    $value =  $values[$data['attribute_option_id']];
                }

            } else {
                $value = __('All Values');
            }

            $fieldset->addField(
                'attribute_option_value',
                'label',
                [
                    'label'   => __('Attribute Value'),
                    'value'   => $value
                ]
            );

        } else {
            $fieldset->addType('mageworx_select', '\MageWorx\SeoAll\Model\Form\Element\Select');

            $fieldset->addField(
                'attribute_option_id',
                'mageworx_select',
                [
                    'name'     => 'attribute_option_id',
                    'label'    => __('Attribute Value'),
                    'values'   => $this->attributeValueOptions->toOptionArray($data['attribute_id']),
                    'required' => true,
                ]
            );
        }

        if ($categoryFilter->getId()) {
            $fieldset->addField(
                'category_path_names',
                'label',
                [
                    'label'   => __('Category Name'),
                    'value'   => $categoryFilter->getCategoryPathNames()
                ]
            );
        } else {
            $fieldset->addType('mageworx_select', '\MageWorx\SeoAll\Model\Form\Element\Select');

            $values = $this->categoryOptions->toOptionArray(
                $categoryFilter->getMarkedCategories(),
                'mark',
                __(' - is already used for "All Values"')
            );

            $fieldset->addField(
                'category_id',
                'mageworx_select',
                [
                    'name'     => 'category_id',
                    'label'    => __('Category Name'),
                    'values'   => $values,
                    'required' => true,
                ]
            );
        }

        $fieldset->addField(
            'store_id',
            'hidden',
            [
                'name'      => 'store_id',
            ]
        );

        $fieldset->addField(
            'store_name',
            'label',
            [
                'label'   => __('Store View Name'),
                'value'   => $this->systemStore->getStoreName($data['store_id'])
            ]
        );


        $fieldset->addField(
            'meta_title',
            'text',
            [
                'name'      => 'meta_title',
                'label'     => __('Meta Title'),
                'title'     => __('Meta Title'),
                'note'      => $this->getNotice()
            ]
        );

        $fieldset->addField(
            'meta_description',
            'text',
            [
                'name'      => 'meta_description',
                'label'     => __('Meta Description'),
                'title'     => __('Meta Description'),
                'note'      => $this->getNotice()
            ]
        );

        $fieldset->addField(
            'meta_keywords',
            'text',
            [
                'name'      => 'meta_keywords',
                'label'     => __('Meta Keywords'),
                'title'     => __('Meta Keywords'),
                'note'      => $this->getNotice()
            ]
        );

        $fieldset->addField(
            'category_seo_name',
            'text',
            [
                'name'      => 'category_seo_name',
                'label'     => __('SEO Name'),
                'title'     => __('SEO Name'),
                'note'      => $this->getNotice()
            ]
        );

        $fieldset->addField(
            'description',
            'editor',
            [
                'name'      => 'description',
                'label'     => __('Description'),
                'title'     => __('Description'),
                'config'    => $this->wysiwygConfig->getConfig(array('add_variables' => false, 'add_widgets' => false))
            ]
        );


        $form->addValues($data);

        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * @param $categoryFilter
     * @return array
     */
    protected function prepareDataForForm($categoryFilter)
    {
        $data = $categoryFilter->getData();

        if ($attributeId = (int)$this->_request->getParam('attribute_id')) {
            $data['attribute_id'] = $attributeId;
        }

        return $data;
    }

    /**
     * @return string
     */
    protected function getNotice()
    {
        if ($this->helperAdapter->isSeoXTemplatesAvailable()) {
            $msg = __('Dynamic variables and randomizer are available.');
            $msg .= '<br>';
            $msg .= __('See the description on the SEO XTemplates Category Filter Edit Page');

            $msg .= ' ' . '(<i>' . __('Marketing');
            $msg .= ' > ' . __('MageWorx SEO Templates');
            $msg .= ' > ' . __('Category Filter Templates');
            $msg .= '</i>)';

            return $msg;
        }
    }
}
