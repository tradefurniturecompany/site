<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\OAuth2;

class Confirm extends \Magento\Framework\View\Element\Template
{

    function getAccount()
    {
        return $this->getRequest()->getParam( 'account' );
    }

    function getCode()
    {
        return $this->getRequest()->getParam( 'code' );
    }

}
