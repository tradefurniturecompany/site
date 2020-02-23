<?php
namespace Hotlink\Framework\Block\Adminhtml\Interactions\Index\Tab;

abstract class AbstractTab extends \Hotlink\Framework\Block\Adminhtml\Tab\Base
{

    /**
     * @var \Hotlink\Framework\Model\Config\Map
     */
    protected $interactionConfigMap;

    function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Hotlink\Framework\Helper\Html\Fieldset $interactionHtmlFieldsetHelper,
        \Hotlink\Framework\Model\Config\Map $interactionConfigMap,
        array $data = []
    )
    {
        $this->interactionConfigMap = $interactionConfigMap;
        parent::__construct( $context,
                             $interactionHtmlFieldsetHelper,
                             $data );
    }

    abstract function initForm( $form, $interaction );

    protected $_tabId;

    protected function _prepareLayout()
    {
        $this->_blockGroup = 'hotlink_framework';
        $this->_controller = 'adminhtml_interactions';
        $this->_mode = 'index';
        parent::_prepareLayout();
    }

    function setTabId( $value )
    {
        $this->_tabId = $value;
    }

    function getTabId()
    {
        return $this->_tabId;
    }

    function getTabOrder()
    {
        if ( $interaction = $this->getInteraction() )
            {
                $index = $this->interactionConfigMap->getIndexOfInteraction( $interaction );
                return $index * 10;
            }
        return 0;
    }

    protected function addButtonPostFrame( $fieldset, $iframe, $label, $add = true )
    {
        $button = $this->addButtonField( $fieldset, 'submit', __( $label ), '', $add );
        $button->setData( 'onclick', $this->getJsPostframe( $iframe ) )->setClass( 'button-field' );
        return $button;
    }

    protected function addButtonClearFrame( $fieldset, $iframe, $label, $add = true )
    {
        $button = $this->addButtonField( $fieldset, 'clear', __( $label ), '', $add );
        $button->setData( 'onclick', $this->getJsClearFrame( $iframe ) )->setClass( 'button-field' );
        return $button;
    }

    protected function addFrame( $fieldset, $before = false )
    {
        $frame = parent::addFrameField( $fieldset, 'frame', $before );
        $frame->setClass( 'form-frame' );
        $frame->setFrameborder( 0 );
        $frame->setStyle( "display:none;" );
        return $frame;
    }

    protected function getJsPostFrame( $iframe, $url = false )
    {
        $action = $this->getTabId();
        if ( !$url )
            {
                $url = $iframe->getForm()->getAction();
            }
        $formId = $iframe->getForm()->getId();
        $frameId = $iframe->getId();
        return "postIFrame( '" . $formId . "', '" . $frameId . "', '" . $url . "');";
    }

    protected function getJsClearFrame( $iframe )
    {
        return "clearIFrame( '" . $iframe->getId() . "');";
    }

    protected function getElementId( $name )
    {
        return $this->getTabId() . '_' . $name;
    }

    protected function getBlock()
    {
        return '';
    }

    function getTabLabel()
    {
        return __(  "Overload ME!" );
    }

    function getTabTitle()
    {
        return __(  "Overload ME!" );
    }

    function canShowTab()
    {
        return true;
    }

    function isHidden()
    {
        return false;
    }

    function getHeaderCssClass()
    {
        return 'icon-head head-asterix';
        return 'icon-head head-sprocket';
    }

}
