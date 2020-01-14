<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\OAuth2;

class Confirm extends \Magento\Framework\View\Element\Template
{

    public function getAccount()
    {
        return $this->getRequest()->getParam( 'account' );
    }

    public function getCode()
    {
        return $this->getRequest()->getParam( 'code' );
    }

}
