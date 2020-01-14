<?php
namespace Hotlink\Framework\Block\Adminhtml\Interactions\Index\Tab;

class Form extends \Magento\Backend\Block\Widget\Form
{

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    )
    {
        parent::__construct(
            $context,
            $data
        );
    }

    protected function _prepareForm()
    {
        return parent::_prepareForm();  // Do nothing here to respect changes applied by tabs
    }

}
