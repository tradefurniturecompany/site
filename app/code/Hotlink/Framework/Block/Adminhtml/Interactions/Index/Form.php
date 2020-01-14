<?php
namespace Hotlink\Framework\Block\Adminhtml\Interactions\Index;

class Form extends \Magento\Backend\Block\Widget\Form
{

    protected function _prepareForm()
    {
        return parent::_prepareForm();  // Do nothing here to respect changes applied by tabs
    }

}
