<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Megamenu
 * @copyright  Copyright (c) 2016 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Megamenu\Block\Adminhtml\Menu\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $_yesno;

    /**
     * @var string
     */
    protected $_template = 'Ves_Megamenu::widget/form.phtml';

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Ves\Megamenu\Model\Config\Source\MobileTempalte
     */
    protected $_mobileTemplate;

    /**
     * @param \Magento\Backend\Block\Template\Context   $context     
     * @param \Magento\Framework\Registry               $registry    
     * @param \Magento\Framework\Data\FormFactory       $formFactory 
     * @param \Magento\Config\Model\Config\Source\Yesno $yesno       
     * @param array                                     $data        
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Config\Model\Config\Source\Yesno $yesno,
        \Magento\Store\Model\System\Store $systemStore,
        \Ves\Megamenu\Helper\Data $viewHelper,
        array $data = []
        ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_yesno = $yesno;
        $this->_systemStore = $systemStore;
        $this->_viewHelper = $viewHelper;
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /**
         * Checking if user have permission to save information
         */
        if($this->_isAllowedAction('Ves_Megamenu::menu_edit')){
            $isElementDisabled = false;
        }else {
            $isElementDisabled = true;
        }

        $this->_eventManager->dispatch(
        'ves_check_license',
        ['obj' => $this,'ex'=>'Ves_Megamenu']
        );
        if(!$this->getData('is_valid') && !$this->getData('local_valid')){
            $isElementDisabled = true;
        }

        $model = $this->_coreRegistry->registry('megamenu_menu');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                    'data'    => [
                    'id'      => 'edit_form',
                    'action'  => $this->getData('action'),
                    'method'  => 'post',
                    'enctype' => 'multipart/form-data'
                ]
            ]
            );

        $fieldset = $form->addFieldset('editortop', ['legend' => '']);
        $field = $fieldset->addField(
            'menutop',
            'textarea',
            [
                'name'     => 'stores[]',
                'label'    => __('Menu Top'),
                'title'    => __('Menu Top'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
            );
        $renderer = $this->getLayout()->createBlock(
            'Ves\Megamenu\Block\Adminhtml\Renderer\Fieldset\EditorTop'
            );
        $field->setRenderer($renderer);

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => '']);
        if ($model->getId()) {
            $fieldset->addField('menu_id', 'hidden', ['name' => 'menu_id']);
        }
        if( $this->_viewHelper->getConfig('general_settings/enable_backup') && (int)$model->getCurrentVersion() !== 0) { 
        $fieldset->addField(
            'current_version',
            'note',
            [
                'name'     => 'current_version',
                'label'    => __('Current Version'),
                'title'    => __('Current Version'),
                'text'     => '#' . $model->getCurrentVersion(),
                'disabled' => $isElementDisabled
            ]
            );
        }
        $fieldset->addField(
            'name',
            'text',
            [
                'name'     => 'name',
                'label'    => __('Name'),
                'title'    => __('Name'),
                'disabled' => $isElementDisabled,
                'required' => true
            ]
            );

        $fieldset->addField(
            'alias',
            'text',
            [
                'name'     => 'alias',
                'label'    => __('Alias'),
                'title'    => __('Alias'),
                'disabled' => $isElementDisabled,
                'required' => true
            ]
            );

        $fieldset->addField(
            'event',
            'select',
            [
                'label'    => __('Event'),
                'title'    => __('Event'),
                'name'     => 'event',
                'options'  => $model->getEventType(),
                'disabled' => $isElementDisabled
            ]
            );

        $fieldset->addField(
            'scrolltofixed',
            'select',
            [
                'label'    => __('Scroll to Fixed'),
                'title'    => __('Scroll to Fixed'),
                'name'     => 'scrolltofixed',
                'options'  => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
            );

        $fieldset->addField(
            'classes',
            'text',
            [
                'name'     => 'classes',
                'label'    => __('Additional Class'),
                'title'    => __('Additional Class'),
                'disabled' => $isElementDisabled
            ]
            );

        $fieldset->addField(
            'disable_above',
            'text',
            [
                'label'    => __('Disable Dimensions (Above)'),
                'title'    => __('Disable Dimensions (Above)'),
                'name'     => 'disable_above',
                'note'     => __('Enter the width(pixel) want to disable this menu. Empty to disable this feature.<br/><br/><strong>Bootstrap 3 Media Query Breakpoints</strong><br/>Large Devices, Wide Screens: 1200px<br/>Medium Devices, Desktops: 992px<br/>Small Devices, Tablets: 768px<br/>Extra Small Devices, Phones: 480px<br/>iPhone Retina: 320px'),
                'disabled' => $isElementDisabled
            ]
            );


        $fieldset->addField(
            'disable_bellow',
            'text',
            [
                'label'    => __('Disable Dimensions'),
                'title'    => __('Disable Dimensions'),
                'name'     => 'disable_bellow',
                'note'     => __('Enter the width(pixel) want to disable this menu. Empty to disable this feature.<br/><br/><strong>Bootstrap 3 Media Query Breakpoints</strong><br/>Large Devices, Wide Screens: 1200px<br/>Medium Devices, Desktops: 992px<br/>Small Devices, Tablets: 768px<br/>Extra Small Devices, Phones: 480px<br/>iPhone Retina: 320px'),
                'disabled' => $isElementDisabled
            ]
            );

        
        $fieldset->addField(
            'desktop_template',
            'select',
            [
                'label'    => __('Desktop Template'),
                'title'    => __('Desktop Template'),
                'name'     => 'desktop_template',
                'options'  => $model->getDesktopTemplates(),
                'disabled' => $isElementDisabled,
                'note'     => __('Apply when width >= 768px')
            ]
            );

        $fieldset->addField(
            'mobile_template',
            'select',
            [
                'label'    => __('Mobile Template'),
                'title'    => __('Mobile Template'),
                'name'     => 'mobile_template',
                'options'  => $model->getMobileTemplates(),
                'disabled' => $isElementDisabled,
                'note'     => __('Apply when width < 768px')
            ]
            );

        $fieldset->addField(
            'mobile_menu_alias',
            'text',
            [
                'label'    => __('Custom Menu Alias for Mobile'),
                'title'    => __('Custom Menu Alias for Mobile'),
                'name'     => 'mobile_menu_alias',
                'disabled' => $isElementDisabled,
                'note'     => __('Enter a menu alias that you want to shown on mobile. Leave to using the current menu')
            ]
            );

        $fieldset->addField(
            'disable_iblocks',
            'select',
            [
                'label'    => __('Disable Item Blocks on mobile'),
                'title'    => __('Disable Item Blocks on mobile'),
                'name'     => 'disable_iblocks',
                'options'  => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled,
                'note'     => __('Item Blocks: Header Block, Left Block, Right Block, Footer Block')
            ]
            );

        $fieldset->addField(
            'customer_group_ids',
            'multiselect',
            [
                'label'    => __('Customer Groups'),
                'title'    => __('Customer Groups'),
                'name'     => 'customer_group_ids[]',
                'values'   => $this->_viewHelper->getCustomerGroups(),
                'disabled' => $isElementDisabled,
                'required' => true
            ]
        );

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name'     => 'stores[]',
                    'label'    => __('Store View'),
                    'title'    => __('Store View'),
                    'required' => true,
                    'values'   => $this->_systemStore->getStoreValuesForForm(false, true),
                    'disabled' => $isElementDisabled
                ]
                );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
                );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
                );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }

        $fieldset->addField(
            'status',
            'select',
            [
                'label'    => __('Status'),
                'title'    => __('Status'),
                'name'     => 'status',
                'options'  => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
            );

        $fieldset = $form->addFieldset('menueditor', ['legend' => '']);
        $field = $fieldset->addField(
            'editor',
            'textarea',
            [
                'name'     => 'stores[]',
                'label'    => __('Store View'),
                'title'    => __('Store View'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
            );
        $renderer = $this->getLayout()->createBlock(
            'Ves\Megamenu\Block\Adminhtml\Renderer\Fieldset\Editor'
            );
        $field->setRenderer($renderer);

        $data = $model->getData();
        if (!$model->getId()) {
            $data['status'] = 1;
            $data['mobile_template'] = 1;
        }
        $form->setValues($data);
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Processing block html after rendering
     *
     * @param string $html
     * @return string
     */
    protected function _afterToHtml($html)
    {
        $form = $this->getForm();
        $htmlIdPrefix = $form->getHtmlIdPrefix();

        /**
         * Form template has possibility to render child block 'form_after', but we can't use it because parent
         * form creates appropriate child block and uses this alias. In this case we can't use the same alias
         * without core logic changes, that's why the code below was moved inside method '_afterToHtml'.
         */
        /** @var $formAfterBlock \Magento\Backend\Block\Widget\Form\Element\Dependence */
        $formAfterBlock = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Form\Element\Dependence',
            'adminhtml.block.widget.form.element.dependence'
        );
        $formAfterBlock->addFieldMap(
            $htmlIdPrefix . 'mobile_template',
            'mobile_template'
        )->addFieldMap(
            $htmlIdPrefix . 'mobile_menu_alias',
            'mobile_menu_alias'
        )->addFieldDependence(
            'mobile_menu_alias',
            'mobile_template',
            2
        );
        $html = $html . $formAfterBlock->toHtml();

        return $html;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}