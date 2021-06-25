<?php
namespace Hotlink\Framework\Block\Adminhtml\Interactions;

class Index extends \Magento\Backend\Block\Widget\Form\Container
{

    protected $registry;
    protected $_platform;

    function __construct( \Magento\Backend\Block\Widget\Context $context,
                                 \Magento\Framework\Registry $registry,
                                 array $data = []
    )
    {
        $this->registry = $registry;

        $this->_objectId = 'hotlink_framework';
        $this->_controller = 'adminhtml_interactions';

        $this->_initInteractionScripts();

        // $this->_blockGroup . '/' . $this->_controller . '_' . $this->_mode . '_form'
        parent::__construct( $context, $data );

        $this->_blockGroup = 'hotlink_framework';
        $this->_mode = 'index';
        $this->setMode( 'index' );

        $this->_initInteractionButtons();
    }

    protected function _initInteractionScripts()
    {
        $this->_formInitScripts[] = "function postIFrame( formid, frameid, url )
{
    var form = document.getElementById( formid );
    form.target = frameid;
    form.action = url;
    var frame = document.getElementById( frameid );
    frame.style.display = 'block';
    frame.style.width = '100%';
    frame.style.height = '1000px';
    form.submit();
}

function setIFrameUrlElement( frameid, urlid )
{
    var frame = document.getElementById( frameid );
    frame.style.display = 'block';
    frame.src = document.getElementById( urlid ).value;
}

function clearIFrame( frameid )
{
    frame = document.getElementById( frameid );
    frame.src = '';
    frame.style.display = 'none';
}";
        return $this;
    }

    protected function _initInteractionButtons()
    {
        $this->removeButton( 'create' );
        $this->removeButton( 'reset' );
        $this->removeButton( 'delete' );
        $this->removeButton( 'save' );
        $this->removeButton( 'back' );
        return $this;
    }

    function getFormInitScripts()
    {
        return parent::getFormInitScripts();
    }

    protected function _getPlatform()
    {
        return $this->registry->registry( 'current_platform' );
    }

    function getFormScripts()
    {
        return parent::getFormScripts();
    }

    protected function _toHtml()
    {
        $html = '';
        $html .= $this->getFormInitScripts();
        $html .= '<div id="interaction_content"></div>';
        $html .= $this->getFormScripts();
        return $html;
    }

    function getTriggerUrl()
    {
        return $this->getUrl('*/' . $this->_controller . '/trigger');
    }

    function getHeaderText()
    {
        return $this->_getPlatform()->getName();
    }

}
