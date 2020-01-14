<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\Authorisation\Form;

class Container extends \Magento\Backend\Block\Widget\Form\Container
{
    protected $_blockGroup = 'Hotlink_Brightpearl';
    protected $_controller = 'authorisation';

    protected function _construct()
    {
        parent::_construct();
        $this->updateButton( 'save', 'label', __( 'Submit' ) );
    }

    public function getHeaderText()
    {
        return __( 'Brightpearl authorisation' );
    }

    public function getFormActionUrl()
    {
        if ($this->hasFormActionUrl()) {
            return $this->getData('form_action_url');
        }
        return $this->getUrl( 'hotlink_brightpearl/authorisation/locate' );
    }

    protected function _buildFormClassName()
    {
        return $this->nameBuilder->buildClassName(
            [$this->_blockGroup, 'Block', 'Adminhtml', $this->_controller, 'Form']
        );
    }

}
