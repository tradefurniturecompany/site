<?php
namespace Hotlink\Framework\Helper;

class Scope
{

    protected $appState;

    function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\State $appState
    )
    {
        $this->appState = $appState;
    }

    function isAdmin()
    {
        return ( $this->appState->getAreaCode() == 'adminhtml' );
    }

}
