<?php
/**
 * Copyright © 2018 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MageWorx\SeoRedirects\Block\Adminhtml\Redirect\Custom\Edit\Tab;

use MageWorx\SeoRedirects\Api\Data\CustomRedirectInterface;
use MageWorx\SeoRedirects\Controller\RegistryConstants;
use MageWorx\SeoRedirects\Model\Redirect\Source\CustomRedirect\Status as StatusOptions;
use MageWorx\SeoRedirects\Model\Redirect\Source\RedirectType as RedirectCodeOptions;
use MageWorx\SeoRedirects\Model\Redirect\Source\RedirectTypeEntity as RequestEntityTypeOptions;
use MageWorx\SeoRedirects\Model\Redirect\CustomRedirectFactory;
use MageWorx\SeoRedirects\Model\Redirect\CustomRedirect;
use MageWorx\SeoRedirects\Model\Redirect\Source\CustomRedirect\RedirectTypeIdentifierFragment;
use MageWorx\SeoRedirects\Model\Redirect\Source\CustomRedirect\RedirectTypeRequestKey as RedirectTypeRequestKeyOptions;
use MageWorx\SeoRedirects\Model\Redirect\Source\CustomRedirect\RedirectTypeTargetKey as RedirectTypeTargetKeyOptions;
use MageWorx\SeoAll\Helper\LandingPage;

class Properties extends \Magento\Widget\Block\Adminhtml\Widget\Options implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    const URL_REQUEST_IDENTIFIER         = 'request_url';
    const PRODUCT_REQUEST_IDENTIFIER     = 'request_product_id';
    const CATEGORY_REQUEST_IDENTIFIER    = 'request_category_id';
    const PAGE_REQUEST_IDENTIFIER        = 'request_page_id';
    const LANDINGPAGE_REQUEST_IDENTIFIER = 'request_landingpage_id';

    const URL_TARGET_IDENTIFIER         = 'target_url';
    const PRODUCT_TARGET_IDENTIFIER     = 'target_product_id';
    const CATEGORY_TARGET_IDENTIFIER    = 'target_category_id';
    const PAGE_TARGET_IDENTIFIER        = 'target_page_id';
    const LANDINGPAGE_TARGET_IDENTIFIER = 'target_landingpage_id';

    /**
     * @var Store
     */
    protected $store;

    /**
     * @var StatusOptions
     */
    protected $statusOptions;

    /**
     * @var RedirectCodeOptions
     */
    protected $redirectCodeOptions;

    /**
     * @var RequestEntityTypeOptions
     */
    protected $requestEntityTypeOptions;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var CustomRedirectFactory
     */
    protected $redirectFactory;

    /**
     * @var RedirectTypeIdentifierFragment
     */
    protected $redirectTypeIdentifierFragmentSource;

    /**
     * @var RedirectTypeRequestKeyOptions
     */
    protected $redirectTypeRequestKeyOptions;

    /**
     * @var RedirectTypeTargetKeyOptions
     */
    protected $redirectTypeTargetKeyOptions;

    /**
     * @var LandingPage
     */
    protected $landingPage;

    /**
     * Properties constructor.
     *
     * @param LandingPage $landingPage
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Option\ArrayPool $sourceModelPool
     * @param \Magento\Widget\Model\Widget $widget
     * @param \Magento\Store\Model\System\Store $store
     * @param StatusOptions $statusOptions
     * @param RedirectCodeOptions $redirectCodeOptions
     * @param RequestEntityTypeOptions $requestEntityTypeOptions
     * @param CustomRedirectFactory $redirectFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param RedirectTypeIdentifierFragment $redirectTypeIdentifierFragmentSource
     * @param RedirectTypeRequestKeyOptions $redirectTypeRequestKeyOptions
     * @param RedirectTypeTargetKeyOptions $redirectTypeTargetKeyOptions
     * @param array $data
     */
    public function __construct(
        LandingPage $landingPage,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Option\ArrayPool $sourceModelPool,
        \Magento\Widget\Model\Widget $widget,
        \Magento\Store\Model\System\Store $store,
        StatusOptions $statusOptions,
        RedirectCodeOptions $redirectCodeOptions,
        RequestEntityTypeOptions $requestEntityTypeOptions,
        CustomRedirectFactory $redirectFactory,
        \Magento\Store\Model\System\Store $systemStore,
        RedirectTypeIdentifierFragment $redirectTypeIdentifierFragmentSource,
        RedirectTypeRequestKeyOptions $redirectTypeRequestKeyOptions,
        RedirectTypeTargetKeyOptions $redirectTypeTargetKeyOptions,
        array $data = []
    ) {
        $this->landingPage                          = $landingPage;
        $this->store                                = $store;
        $this->statusOptions                        = $statusOptions;
        $this->redirectCodeOptions                  = $redirectCodeOptions;
        $this->requestEntityTypeOptions             = $requestEntityTypeOptions;
        $this->systemStore                          = $systemStore;
        $this->redirectFactory                      = $redirectFactory;
        $this->redirectTypeIdentifierFragmentSource = $redirectTypeIdentifierFragmentSource;
        $this->redirectTypeRequestKeyOptions        = $redirectTypeRequestKeyOptions;
        $this->redirectTypeTargetKeyOptions         = $redirectTypeTargetKeyOptions;
        parent::__construct($context, $registry, $formFactory, $sourceModelPool, $widget, $data);
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Custom Redirect Properties');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Custom Redirect Properties');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Getter
     *
     * @return \MageWorx\SeoRedirects\Model\Redirect\CustomRedirect
     */
    protected function getRedirectInstance()
    {
        $redirect = $this->_coreRegistry->registry(RegistryConstants::CURRENT_REDIRECT_CONSTANT);

        if (!$redirect) {
            $redirect = $this->redirectFactory->create();
        }

        return $redirect;
    }

    public function addFields()
    {
        return $this;
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $this->getForm()->setUseContainer(false);

        /** @var \MageWorx\SeoRedirects\Model\Redirect\CustomRedirect $redirect */
        $redirect = $this->getRedirectInstance();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id'     => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post'
                ]
            ]
        );

        $form->setFieldNameSuffix(RegistryConstants::CUSTOM_REDIRECT_FORM_DATA_KEY);
        $form->setUseContainer(true);

        $legend = __('SEO Redirect Settings');

        if ($redirect->getStoreId() !== null) {
            $storeName = $this->_storeManager->getStore($redirect->getStoreId())->getName();
            $legend    .= ' ' . __('for %1', $this->escapeHtml($storeName));
        }

        /** @var  \Magento\Framework\Data\Form\Element\Fieldset $fieldset */
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => $legend,
                'class'  => 'fieldset-wide'
            ]
        );

        if ($redirect->getId()) {
            $fieldset->addField(
                CustomRedirectInterface::REDIRECT_ID,
                'hidden',
                ['name' => CustomRedirectInterface::REDIRECT_ID]
            );
        }

        $this->setMainFieldsetHtmlId('mageworx_custom_redirects');

        $fieldset->addField(
            CustomRedirectInterface::STATUS,
            'select',
            [
                'label'   => __('Status'),
                'title'   => __('Status'),
                'name'    => CustomRedirectInterface::STATUS,
                'options' => $this->statusOptions->toArray()
            ]
        );

        $this->addStoreViewField($redirect, $fieldset);

        $fieldset->addField(
            CustomRedirectInterface::REDIRECT_CODE,
            'select',
            [
                'label'   => __('Redirect Code'),
                'title'   => __('Redirect Code'),
                'name'    => CustomRedirectInterface::REDIRECT_CODE,
                'options' => $this->redirectCodeOptions->toArray()
            ]
        );


        $fieldset->addField(
            'from',
            'label',
            [
                'label' => __('Redirect From:'),
            ]
        );

        $requestEntityType = $fieldset->addField(
            CustomRedirectInterface::REQUEST_ENTITY_TYPE,
            'select',
            [
                'name'    => CustomRedirectInterface::REQUEST_ENTITY_TYPE,
                'label'   => __('Type'),
                'title'   => __('Type'),
                'options' => $this->requestEntityTypeOptions->toArray()
            ]
        );

        $requestUrlComment = __(
            "Store URL part will be added automatically:<br>
                            'my/custom/url' will be converted to 'http[s]://(store_URL_here)/my/custom/url'"
        );

        $requestUrlValue = '';
        if ($redirect->getRequestEntityType() == CustomRedirect::REDIRECT_TYPE_CUSTOM) {
            $requestUrlValue = $redirect->getRequestEntityIdentifier();
        }

        $requestUrl = $fieldset->addField(
            RedirectTypeRequestKeyOptions::URL_REQUEST_IDENTIFIER,
            'text',
            [
                'name'     => RedirectTypeRequestKeyOptions::URL_REQUEST_IDENTIFIER,
                'label'    => __('URL'),
                'title'    => __('URL'),
                'required' => true,
                'value'    => $requestUrlValue,
                'note'     => $requestUrlComment
            ]
        );

        $this->addCategoryChooserField(
            $fieldset,
            RedirectTypeRequestKeyOptions::CATEGORY_REQUEST_IDENTIFIER,
            $this->getConvertedRequestEntityIdentifier($redirect, CustomRedirect::REDIRECT_TYPE_CATEGORY)
        );

        $this->addProductChooserField(
            $fieldset,
            RedirectTypeRequestKeyOptions::PRODUCT_REQUEST_IDENTIFIER,
            $this->getConvertedRequestEntityIdentifier($redirect, CustomRedirect::REDIRECT_TYPE_PRODUCT)
        );

        $this->addPageChooserField(
            $fieldset,
            RedirectTypeRequestKeyOptions::PAGE_REQUEST_IDENTIFIER,
            $this->getConvertedRequestEntityIdentifier($redirect, CustomRedirect::REDIRECT_TYPE_PAGE)
        );

        if ($this->landingPage->isLandingPageEnabled()) {
            $this->addLandingPageChooserField(
                $fieldset,
                RedirectTypeRequestKeyOptions::LANDINGPAGE_REQUEST_IDENTIFIER,
                $this->getConvertedRequestEntityIdentifier($redirect, CustomRedirect::REDIRECT_TYPE_LANDINGPAGE)
            );
        }

        $fieldset->addField(
            'to',
            'label',
            [
                'label' => __('Redirect To:'),
            ]
        );

        $targetEntityType = $fieldset->addField(
            CustomRedirectInterface::TARGET_ENTITY_TYPE,
            'select',
            [
                'name'    => CustomRedirectInterface::TARGET_ENTITY_TYPE,
                'label'   => __('Type'),
                'title'   => __('Type'),
                'options' => $this->requestEntityTypeOptions->toArray()
            ]
        );

        $targetUrlComment = __(
            "Link without 'http[s]://' as 'my/custom/url' 
                            will be converted to 'http[s]://(store_URL_here)/my/custom/url'<br>
                            Link with 'http[s]://' will be added as it is."
        );

        $targetUrlValue = '';
        if ($redirect->getTargetEntityType() == CustomRedirect::REDIRECT_TYPE_CUSTOM) {
            $targetUrlValue = $redirect->getTargetEntityIdentifier();
        }

        $targetUrl = $fieldset->addField(
            RedirectTypeTargetKeyOptions::URL_TARGET_IDENTIFIER,
            'text',
            [
                'name'     => RedirectTypeTargetKeyOptions::URL_TARGET_IDENTIFIER,
                'label'    => __('URL'),
                'title'    => __('URL'),
                'required' => true,
                'value'    => $targetUrlValue,
                'note'     => $targetUrlComment
            ]
        );

        $this->addCategoryChooserField(
            $fieldset,
            RedirectTypeTargetKeyOptions::CATEGORY_TARGET_IDENTIFIER,
            $this->getConvertedTargetEntityIdentifier($redirect, CustomRedirect::REDIRECT_TYPE_CATEGORY)
        );

        $this->addProductChooserField(
            $fieldset,
            RedirectTypeTargetKeyOptions::PRODUCT_TARGET_IDENTIFIER,
            $this->getConvertedTargetEntityIdentifier($redirect, CustomRedirect::REDIRECT_TYPE_PRODUCT)
        );

        $this->addPageChooserField(
            $fieldset,
            RedirectTypeTargetKeyOptions::PAGE_TARGET_IDENTIFIER,
            $this->getConvertedTargetEntityIdentifier($redirect, CustomRedirect::REDIRECT_TYPE_PAGE)
        );

        if ($this->landingPage->isLandingPageEnabled()) {
            $this->addLandingPageChooserField(
                $fieldset,
                RedirectTypeTargetKeyOptions::LANDINGPAGE_TARGET_IDENTIFIER,
                $this->getConvertedTargetEntityIdentifier($redirect, CustomRedirect::REDIRECT_TYPE_LANDINGPAGE)
            );
        }

        $this->addJsDependency($targetEntityType);
        $this->addDependency($requestEntityType, $targetEntityType, $requestUrl, $targetUrl);

        if ($redirect->getId()) {
            $redirectData = $redirect->getData();
        } else {
            $redirectData = $redirect->getDefaultValues();
        }

        $form->addValues($redirectData);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\Fieldset $fieldset
     * @return $this
     */
    protected function addCategoryChooserField($fieldset, $name, $value = null)
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
     * @param \Magento\Framework\Data\Form\Element\Fieldset $fieldset
     * @return $this
     */
    protected function addProductChooserField($fieldset, $name, $value = null)
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
            '\MageWorx\SeoRedirects\Block\Adminhtml\Widget\Chooser\Product',
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
     * @param \Magento\Framework\Data\Form\Element\Fieldset $fieldset
     * @return $this
     */
    protected function addPageChooserField($fieldset, $name, $value = null)
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
     * @param \Magento\Framework\Data\Form\Element\Fieldset $fieldset
     * @return $this
     */
    protected function addLandingPageChooserField($fieldset, $name, $value = null)
    {
        $field = $fieldset->addField(
            $name,
            'label',
            [
                'name'     => $name,
                'label'    => __('Select Landing Page...'),
                'required' => true,
                'class'    => 'widget-option',
                'value'    => $value,
            ]
        );

        $helperBlock = $this->getLayout()->createBlock(
            '\MageWorx\SeoAll\Block\Adminhtml\LandingPage\LandingpageGrid',
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
     * @param $requestEntityType
     * @param $targetEntityType
     * @param $requestUrl
     * @param $targetUrl
     * @return $this
     */
    protected function addDependency($requestEntityType, $targetEntityType, $requestUrl, $targetUrl)
    {
        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Form\Element\Dependence'
            )
                 ->addFieldMap($requestEntityType->getHtmlId(), $requestEntityType->getName())
                 ->addFieldMap($requestUrl->getHtmlId(), $requestUrl->getName())
                 ->addFieldMap($targetEntityType->getHtmlId(), $targetEntityType->getName())
                 ->addFieldMap($targetUrl->getHtmlId(), $targetUrl->getName())
                 ->addFieldDependence(
                     $requestUrl->getName(),
                     $requestEntityType->getName(),
                     CustomRedirect::REDIRECT_TYPE_CUSTOM
                 )
                 ->addFieldDependence(
                     $targetUrl->getName(),
                     $targetEntityType->getName(),
                     CustomRedirect::REDIRECT_TYPE_CUSTOM
                 )
        );

        return $this;
    }

    /**
     * @param $targetEntityType
     * @return $this
     */
    protected function addJsDependency($targetEntityType)
    {
        $requestOptions = json_encode($this->getRedirectTypeRequestKeyEntityOptions());
        $targetOptions  = json_encode($this->getRedirectTypeTargetKeyEntityOptions());

        $targetEntityType->setAfterElementHtml(
            "<script>

            require(['jquery'], function($) {
               
                var requestOptions = $requestOptions;
                var targetOptions  = $targetOptions;
                
                function actionChooser(action, entityName) {
                    
                    var inputElementClass = '.field-chooser' + entityName + ' input';
                    var fieldClass = '.field-' + entityName;
                    var chooserClass = '.field-chooser' + entityName;
                             
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
                    
                $('#request_entity_type').on('change', function() {
                    
                    for (key in requestOptions) {
                        actionChooser('hide', requestOptions[key]);
                    }                    
                    var value = $(this).val();
                    actionChooser('show', requestOptions[value]);
                });
                
                $('#target_entity_type').on('change', function() {                                                    
                    
                    for (key in targetOptions) {
                        actionChooser('hide', targetOptions[key]);
                    }                    
                    var value = $(this).val();
                    actionChooser('show', targetOptions[value]);
                });
                
                $('document').ready(function () {
                   $('#request_entity_type').trigger('change');
                   $('#target_entity_type').trigger('change');
                });
            }
        );
        </script>"
        );

        return $this;
    }

    /**
     * @param $redirect
     * @param $fieldset
     * @return void
     */
    protected function addStoreViewField($redirect, $fieldset)
    {
        if (!$redirect->getId()) {
            if ($this->_storeManager->isSingleStoreMode()) {
                $fieldset->addField(
                    'store_id',
                    'hidden',
                    [
                        'name'  => 'stores[]',
                        'value' => $this->_storeManager->getStore(true)->getId()
                    ]
                );
                $redirect->setStoreId($this->_storeManager->getStore(true)->getId());
            } else {
                $fieldset->addField(
                    'store_id',
                    'multiselect',
                    [
                        'name'     => 'stores[]',
                        'label'    => __('Store View'),
                        'title'    => __('Store View'),
                        'required' => true,
                        'values'   => $this->store->getStoreValuesForForm(false, true),
                    ]
                );
            }
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                [
                    'name'  => 'store_id',
                    'value' => $this->_storeManager->getStore(true)->getId()
                ]
            );
        }
    }

    /**
     * @param \MageWorx\SeoRedirects\Model\Redirect\CustomRedirect $redirect
     * @param int $entityType
     * @return string
     */
    protected function getConvertedRequestEntityIdentifier($redirect, $entityType)
    {
        if (!$redirect->getRequestEntityType()) {
            return '';
        }

        if ($entityType != $redirect->getRequestEntityType()) {
            return '';
        }

        $redirectTypeIdentifierFragments = $this->redirectTypeIdentifierFragmentSource->toArray();

        $requestEntityIdentifier =
            $redirectTypeIdentifierFragments[$redirect->getRequestEntityType()] .
            $redirect->getRequestEntityIdentifier();

        return $requestEntityIdentifier;
    }


    /**
     * @param \MageWorx\SeoRedirects\Model\Redirect\CustomRedirect $redirect
     * @param int $entityType
     * @return string
     */
    protected function getConvertedTargetEntityIdentifier($redirect, $entityType)
    {
        if (!$redirect->getTargetEntityType()) {
            return '';
        }

        if ($entityType != $redirect->getTargetEntityType()) {
            return '';
        }

        $redirectTypeIdentifierFragments = $this->redirectTypeIdentifierFragmentSource->toArray();

        $targetEntityIdentifier =
            $redirectTypeIdentifierFragments[$redirect->getTargetEntityType()] .
            $redirect->getTargetEntityIdentifier();

        return $targetEntityIdentifier;
    }

    /**
     * @return array
     */
    protected function getRedirectTypeTargetKeyEntityOptions()
    {
        $targetKeyOptions = $this->redirectTypeTargetKeyOptions->toArray();
        unset($targetKeyOptions[CustomRedirect::REDIRECT_TYPE_CUSTOM]);

        return $targetKeyOptions;
    }

    /**
     * @return array
     */
    protected function getRedirectTypeRequestKeyEntityOptions()
    {
        $requestKeyOptions = $this->redirectTypeRequestKeyOptions->toArray();
        unset($requestKeyOptions[CustomRedirect::REDIRECT_TYPE_CUSTOM]);

        return $requestKeyOptions;
    }
}
