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
namespace Ves\Megamenu\Block\Adminhtml\Menu;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Ves\Megamenu\Helper\Data
     */
    protected $_vesData;

    /**
     * @param \Magento\Backend\Block\Widget\Context       $context        
     * @param \Magento\Framework\Registry                 $registry       
     * @param \Magento\Framework\Message\ManagerInterface $messageManager 
     * @param \Ves\Megamenu\Helper\Data                   $vesData        
     * @param array                                       $data           
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Ves\Megamenu\Helper\Data $vesData,
        array $data = []
        ) {
        $this->_coreRegistry = $registry;
        $this->messageManager = $messageManager;
        $this->_vesData = $vesData;
        parent::__construct($context, $data);
    }

    /**
     * Initialize cms page edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'menu_id';
        $this->_blockGroup = 'Ves_Megamenu';
        $this->_controller = 'adminhtml_menu';

        parent::_construct();

        if ($this->_isAllowedAction('Ves_Megamenu::menu_save')) {
            $this->buttonList->update('save', 'label', __('Save Menu'));
            if($this->_coreRegistry->registry('megamenu_menu')->getId()){
                $this->buttonList->add(
                    'duplicate',
                    [
                    'label' => __('Save and Duplicate'),
                    'class' => 'save'
                    ],
                    -50
                    );
            }

            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => [
                                'event' => 'saveAndContinueEdit',
                                'target' => '#edit_form'
                            ],
                        ],
                    ]
                ],
                -100
                );

        $model = $this->_coreRegistry->registry('megamenu_menu');
        $currentVersion = (int)$model->getCurrentVersion();

        if($currentVersion!==0 && $this->_vesData->getConfig('general_settings/enable_backup')){
            if($currentVersion>1){
            $this->buttonList->add(
                'revertpreviousversion',
                [
                    'label' => __('Revert Previous Version'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => [
                                'event' => 'revertpreviousversion',
                                'target' => '#edit_form'
                            ],
                        ],
                    ]
                ],
                0
                );
            }

            $this->buttonList->add(
                'revertnextversion',
                [
                    'label' => __('Revert Next Version'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => [
                                'event' => 'revertnextversion',
                                'target' => '#edit_form'
                            ],
                        ],
                    ]
                ],
                0
                );
        }
        } else {
            $this->buttonList->remove('save');
        }

        if ($this->_isAllowedAction('Ves_Megamenu::menu_delete')) {
            $this->buttonList->update('delete', 'label', __('Delete Menu'));
        } else {
            $this->buttonList->remove('delete');
        }

        
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('megamenu_menu')->getId()) {
            return __("Edit Menu '%1'", $this->escapeHtml($this->_coreRegistry->registry('megamenu_menu')->getTitle()));
        } else {
            return __('New Menu');
        }
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

    protected function _toHtml(){
        $this->_eventManager->dispatch(
         'ves_check_license',
         ['obj' => $this,'ex'=>'Ves_Megamenu']
         );
        return parent::_toHtml();
    }

    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $model = $this->_coreRegistry->registry('megamenu_menu');
        $this->_formScripts[] = "
        require([
        'jquery',
        'Magento_Ui/js/modal/modal',
        'mage/backend/form'
        ], function(){
            jQuery('#duplicate').click(function(){
                var actionUrl = jQuery('#edit_form').attr('action') + 'duplicate/1';
                jQuery('#edit_form').attr('action', actionUrl);
                jQuery('#edit_form').submit();
            }); ";

        if($model){
            $currentVersion = (int)$model->getCurrentVersion();
            if($currentVersion>1){
                $this->_formScripts[] .= "jQuery('#revertpreviousversion').click(function(){
                        var actionUrl = jQuery('#edit_form').attr('action') + 'revert_previous/" . ($currentVersion-1) . "';
                        jQuery('#edit_form').attr('action', actionUrl);
                        jQuery('#edit_form').submit();
                    });";
            }

            $this->_formScripts[] .= "jQuery('#revertnextversion').click(function(){
                    var actionUrl = jQuery('#edit_form').attr('action') + 'revert_next/" . ($currentVersion+1) . "';
                    jQuery('#edit_form').attr('action', actionUrl);
                    jQuery('#edit_form').submit();
                });";
        }

        $this->_formScripts[] .= "function toggleEditor() {
                if (tinyMCE.getInstanceById('before_form_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'before_form_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'before_form_content');
                }
            };
        });";
        return parent::_prepareLayout();
    }
}