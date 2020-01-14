<?php
namespace Magesales\Shippingtable\Block\Adminhtml;
class Method extends \Magento\Backend\Block\Widget\Grid\Container
{
    public function _construct()
    {
        $this->_controller = 'adminhtml_method';
        $this->_blockGroup = 'Magesales_Shippingtable';
        $this->_headerText = __('Methods');
        $this->_addButtonLabel = __('Add Method');
        parent::_construct();
    }
}