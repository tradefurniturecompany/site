<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Block\Adminhtml\Canonical\Custom\Edit\Tab;

use MageWorx\SeoBase\Api\Data\CustomCanonicalInterface;
use MageWorx\SeoBase\Model\CustomCanonical;
use MageWorx\SeoBase\Model\Source\CustomCanonical\SourceTypeEntity as SourceTypeEntityOptions;
use MageWorx\SeoBase\Model\Source\CustomCanonical\TargetTypeEntity as TargetTypeEntityOptions;
use MageWorx\SeoBase\Model\Source\CustomCanonical\TargetStoreId as TargetStoreIdOptions;
use MageWorx\SeoBase\Helper\CustomCanonical as HelperCustomCanonical;
use Magento\Framework\Data\Form\Element\Fieldset;
use Magento\Backend\Block\Widget\Form\Generic as FormGeneric;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Store\Model\System\Store as SystemStore;
use Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite;

class Information extends FormGeneric implements TabInterface
{
    /**
     * @var SystemStore
     */
    private $systemStore;

    /**
     * @var \MageWorx\SeoBase\Model\CustomCanonicalFactory
     */
    private $customCanonicalFactory;

    /**
     * @var SourceTypeEntityOptions
     */
    private $sourceTypeEntityOptions;

    /**
     * @var TargetTypeEntityOptions
     */
    private $targetTypeEntityOptions;

    /**
     * @var TargetStoreIdOptions
     */
    private $targetStoreIdOptions;

    /**
     * @var HelperCustomCanonical
     */
    private $helperCustomCanonical;

    /**
     * Information constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param SystemStore $systemStore
     * @param \MageWorx\SeoBase\Model\CustomCanonicalFactory $customCanonicalFactory
     * @param SourceTypeEntityOptions $sourceTypeEntityOptions
     * @param TargetTypeEntityOptions $targetTypeEntityOptions
     * @param TargetStoreIdOptions $targetStoreIdOptions
     * @param HelperCustomCanonical $helperCustomCanonical
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        SystemStore $systemStore,
        \MageWorx\SeoBase\Model\CustomCanonicalFactory $customCanonicalFactory,
        SourceTypeEntityOptions $sourceTypeEntityOptions,
        TargetTypeEntityOptions $targetTypeEntityOptions,
        TargetStoreIdOptions $targetStoreIdOptions,
        HelperCustomCanonical $helperCustomCanonical,
        array $data = []
    ) {
        $this->systemStore             = $systemStore;
        $this->customCanonicalFactory  = $customCanonicalFactory;
        $this->sourceTypeEntityOptions = $sourceTypeEntityOptions;
        $this->targetTypeEntityOptions = $targetTypeEntityOptions;
        $this->targetStoreIdOptions    = $targetStoreIdOptions;
        $this->helperCustomCanonical   = $helperCustomCanonical;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Return Tab Label
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getTabLabel()
    {
        return __('Canonical Information');
    }

    /**
     * Return Tab Title
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getTabTitle()
    {
        return __('Canonical Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare form fields
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $customCanonical = $this->getCustomCanonicalInstance();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setFieldNameSuffix(CustomCanonical::CUSTOM_CANONICAL_FORM_DATA_KEY);

        $baseFieldset = $form->addFieldset('base_fieldset', ['legend' => __('Canonical Information')]);

        if ($customCanonical->getId()) {
            $baseFieldset->addField(
                CustomCanonicalInterface::ENTITY_ID,
                'hidden',
                [
                    'name' => CustomCanonicalInterface::ENTITY_ID,
                ]
            );
        }

        $baseFieldset->addField(
            'from',
            'label',
            [
                'label' => __('Canonical From:'),
            ]
        );

        $this->addSourceStoreIdField($customCanonical, $baseFieldset);

        $sourceEntityType = $baseFieldset->addField(
            CustomCanonicalInterface::SOURCE_ENTITY_TYPE,
            'select',
            [
                'name'     => CustomCanonicalInterface::SOURCE_ENTITY_TYPE,
                'label'    => __('Type'),
                'title'    => __('Type'),
                'options'  => $this->sourceTypeEntityOptions->toArray(),
                'required' => true
            ]
        );

        $sourceChooserTypeOptions = $this->helperCustomCanonical->getSourceChooserTypeOptions();

        $this->addProductChooserField(
            $baseFieldset,
            $sourceChooserTypeOptions[Rewrite::ENTITY_TYPE_PRODUCT],
            $this->getConvertedSourceEntityId(
                $customCanonical,
                Rewrite::ENTITY_TYPE_PRODUCT,
                HelperCustomCanonical::PRODUCT_CHOOSER_VALUE_PREFIX
            )
        );

        $baseFieldset->addField(
            'to',
            'label',
            [
                'label' => __('Canonical To:'),
            ]
        );

        $this->addTargetStoreIdField($customCanonical, $baseFieldset);

        $targetEntityType = $baseFieldset->addField(
            CustomCanonicalInterface::TARGET_ENTITY_TYPE,
            'select',
            [
                'name'     => CustomCanonicalInterface::TARGET_ENTITY_TYPE,
                'label'    => __('Type'),
                'title'    => __('Type'),
                'options'  => $this->targetTypeEntityOptions->toArray(),
                'required' => true
            ]
        );

        $customUrlComment = __(
            "Link without 'http[s]://' as 'my/custom/url' 
            will be converted to 'http[s]://(store_URL_here)/my/custom/url'<br>
            Link with 'http[s]://' will be added as it is."
        );

        $customUrl = $baseFieldset->addField(
            CustomCanonicalInterface::TARGET_ENTITY_ID,
            'text',
            [
                'name'     => CustomCanonicalInterface::TARGET_ENTITY_ID,
                'label'    => __('URL'),
                'title'    => __('URL'),
                'required' => true,
                'note'     => $customUrlComment
            ]
        );

        $targetChooserTypeOptions = $this->helperCustomCanonical->getTargetChooserTypeOptions();

        $this->addProductChooserField(
            $baseFieldset,
            $targetChooserTypeOptions[Rewrite::ENTITY_TYPE_PRODUCT],
            $this->getConvertedTargetEntityId(
                $customCanonical,
                Rewrite::ENTITY_TYPE_PRODUCT,
                HelperCustomCanonical::PRODUCT_CHOOSER_VALUE_PREFIX
            )
        );

        $this->addCategoryChooserField(
            $baseFieldset,
            $targetChooserTypeOptions[Rewrite::ENTITY_TYPE_CATEGORY],
            $this->getConvertedTargetEntityId(
                $customCanonical,
                Rewrite::ENTITY_TYPE_CATEGORY,
                HelperCustomCanonical::CATEGORY_CHOOSER_VALUE_PREFIX
            )
        );

        $this->addPageChooserField(
            $baseFieldset,
            $targetChooserTypeOptions[Rewrite::ENTITY_TYPE_CMS_PAGE],
            $this->getConvertedTargetEntityId(
                $customCanonical,
                Rewrite::ENTITY_TYPE_CMS_PAGE
            )
        );

        $this->addDependency($targetEntityType, $customUrl);
        $this->addJsDependency($targetEntityType);


        if ($customCanonical->getId()) {
            $form->addValues($customCanonical->getData());
        }

        $this->setForm($form);

        parent::_prepareForm();

        return $this;
    }

    /**
     * @return CustomCanonical
     */
    private function getCustomCanonicalInstance()
    {
        /** @var CustomCanonical $customCanonical */
        $customCanonical = $this->_coreRegistry->registry(CustomCanonical::CURRENT_CUSTOM_CANONICAL);

        if (!$customCanonical) {
            return $this->customCanonicalFactory->create();
        }

        return $customCanonical;
    }

    /**
     * @param Fieldset $fieldset
     * @param string $name
     * @param string $value
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function addProductChooserField($fieldset, $name, $value = '')
    {
        $field = $fieldset->addField(
            $name,
            'label',
            [
                'name'     => $name,
                'label'    => __('Select Product...'),
                'required' => true,
                'class'    => 'widget-option',
                'value'    => $value,
            ]
        );

        $helperBlock = $this->getLayout()->createBlock(
            '\MageWorx\SeoBase\Block\Adminhtml\Widget\Chooser\Product',
            '',
            ['data' => []]
        );

        if ($helperBlock instanceof \Magento\Framework\DataObject) {
            $helperBlock->setConfig(
                []
            )->setFieldsetId(
                $fieldset->getId()
            )->prepareElementHtml(
                $field
            );
        }

        return $field;
    }

    /**
     * @param Fieldset $fieldset
     * @param string $name
     * @param string $value
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function addCategoryChooserField($fieldset, $name, $value = '')
    {
        $field = $fieldset->addField(
            $name,
            'label',
            [
                'name'     => $name,
                'label'    => __('Select Category...'),
                'required' => true,
                'class'    => 'widget-option',
                'value'    => $value,
            ]
        );

        $helperBlock = $this->getLayout()->createBlock(
            '\Magento\Catalog\Block\Adminhtml\Category\Widget\Chooser',
            '',
            ['data' => []]
        );

        $helperBlock->setConfig(
            []
        )->setFieldsetId(
            $fieldset->getId()
        )->prepareElementHtml(
            $field
        );

        return $field;
    }

    /**
     * @param Fieldset $fieldset
     * @param string $name
     * @param string $value
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function addPageChooserField($fieldset, $name, $value = '')
    {
        $field = $fieldset->addField(
            $name,
            'label',
            [
                'name'     => $name,
                'label'    => __('Select Page...'),
                'required' => true,
                'class'    => 'widget-option',
                'value'    => $value,
            ]
        );

        $helperBlock = $this->getLayout()->createBlock(
            '\Magento\Cms\Block\Adminhtml\Page\Widget\Chooser',
            '',
            ['data' => []]
        );

        $helperBlock->setConfig(
            []
        )->setFieldsetId(
            $fieldset->getId()
        )->prepareElementHtml(
            $field
        );

        return $field;
    }

    /**
     * @param CustomCanonicalInterface $customCanonical
     * @param Fieldset $fieldset
     */
    private function addSourceStoreIdField($customCanonical, $fieldset)
    {
        if (!$customCanonical->getId()) {

            if ($this->_storeManager->isSingleStoreMode()) {
                $fieldset->addField(
                    CustomCanonicalInterface::SOURCE_STORE_ID,
                    'hidden',
                    [
                        'name'  => CustomCanonicalInterface::SOURCE_STORE_ID,
                        'value' => \Magento\Store\Model\Store::DEFAULT_STORE_ID
                    ]
                );
            } else {
                $fieldset->addField(
                    CustomCanonicalInterface::SOURCE_STORE_ID,
                    'multiselect',
                    [
                        'name'     => 'source_stores[]',
                        'label'    => __('Store View'),
                        'title'    => __('Store View'),
                        'required' => true,
                        'values'   => $this->systemStore->getStoreValuesForForm(false, true)
                    ]
                );
            }
        } else {
            $fieldset->addField(
                CustomCanonicalInterface::SOURCE_STORE_ID,
                'select',
                [
                    'name'     => CustomCanonicalInterface::SOURCE_STORE_ID,
                    'label'    => __('Store View'),
                    'title'    => __('Store View'),
                    'required' => true,
                    'values'   => $this->systemStore->getStoreValuesForForm(false, true)
                ]
            );
        }
    }

    /**
     * @param CustomCanonicalInterface $customCanonical
     * @param Fieldset $fieldset
     */
    private function addTargetStoreIdField($customCanonical, $fieldset)
    {
        if (!$customCanonical->getId()) {

            if ($this->_storeManager->isSingleStoreMode()) {
                $fieldset->addField(
                    CustomCanonicalInterface::TARGET_STORE_ID,
                    'hidden',
                    [
                        'name'  => CustomCanonicalInterface::TARGET_STORE_ID,
                        'value' => TargetStoreIdOptions::SAME_AS_SOURCE_ENTITY
                    ]
                );
            } else {
                $fieldset->addField(
                    CustomCanonicalInterface::TARGET_STORE_ID,
                    'select',
                    [
                        'name'     => CustomCanonicalInterface::TARGET_STORE_ID,
                        'label'    => __('Store View'),
                        'title'    => __('Store View'),
                        'required' => true,
                        'options'  => $this->targetStoreIdOptions->toArray()
                    ]
                );
            }
        } else {
            $fieldset->addField(
                CustomCanonicalInterface::TARGET_STORE_ID,
                'select',
                [
                    'name'     => CustomCanonicalInterface::TARGET_STORE_ID,
                    'label'    => __('Store View'),
                    'title'    => __('Store View'),
                    'required' => true,
                    'options'  => $this->targetStoreIdOptions->toArray()
                ]
            );
        }
    }

    /**
     * @param CustomCanonicalInterface $customCanonical
     * @param string $entityType
     * @param string $valuePrefix
     * @return string
     */
    private function getConvertedSourceEntityId($customCanonical, $entityType, $valuePrefix = '')
    {
        if ($customCanonical->getSourceEntityType() != $entityType) {
            return '';
        }

        return $valuePrefix . $customCanonical->getSourceEntityId();
    }

    /**
     * @param CustomCanonicalInterface $customCanonical
     * @param string $entityType
     * @param string $valuePrefix
     * @return string
     */
    private function getConvertedTargetEntityId($customCanonical, $entityType, $valuePrefix = '')
    {
        if ($customCanonical->getTargetEntityType() != $entityType) {
            return '';
        }

        return $valuePrefix . $customCanonical->getTargetEntityId();
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $targetEntityType
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $customUrl
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function addDependency($targetEntityType, $customUrl)
    {
        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
                 ->addFieldMap($targetEntityType->getHtmlId(), $targetEntityType->getName())
                 ->addFieldMap($customUrl->getHtmlId(), $customUrl->getName())
                 ->addFieldDependence(
                     $customUrl->getName(),
                     $targetEntityType->getName(),
                     Rewrite::ENTITY_TYPE_CUSTOM
                 )
        );

        return $this;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $targetEntityType
     * @return $this
     */
    private function addJsDependency($targetEntityType)
    {
        $sourceOptions = json_encode($this->helperCustomCanonical->getSourceChooserTypeOptions());
        $targetOptions = json_encode($this->helperCustomCanonical->getTargetChooserTypeOptions());

        $targetEntityType->setAfterElementHtml(
            "<script>

            require(['jquery'], function($) {
               
                var sourceOptions = $sourceOptions;
                var targetOptions = $targetOptions;
                
                function actionChooser(action, entityName) {
                    
                    var inputElementClass = '.field-chooser' + entityName + ' input';
                    var fieldClass        = '.field-' + entityName;
                    var chooserClass      = '.field-chooser' + entityName;
                             
                    if (action == 'hide') {
                        $(inputElementClass).removeClass('required-entry');
                        $(fieldClass).hide();                    
                        $(chooserClass).hide();
                    } 
                    
                    if (action == 'show') {                    
                        $(inputElementClass).addClass('required-entry');
                        $(fieldClass).show();                    
                        $(chooserClass).show();
                    }
                }
                    
                $('#source_entity_type').on('change', function() {
                    
                    for (key in sourceOptions) {
                        actionChooser('hide', sourceOptions[key]);
                    }                    
                    var value = $(this).val();
                    actionChooser('show', sourceOptions[value]);
                });
                
                $('#target_entity_type').on('change', function() {                                                    
                    
                    for (key in targetOptions) {
                        actionChooser('hide', targetOptions[key]);
                    }                    
                    var value = $(this).val();
                    actionChooser('show', targetOptions[value]);
                });
                
                $('document').ready(function () {
                   $('#source_entity_type').trigger('change');
                   $('#target_entity_type').trigger('change');
                });
            }
        );
        </script>"
        );

        return $this;
    }
}
