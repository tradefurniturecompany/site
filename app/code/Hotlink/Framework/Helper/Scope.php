<?php
namespace Hotlink\Framework\Helper;

class Scope
{

    protected $appState;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\State $appState
    )
    {
        $this->appState = $appState;
    }

    public function isAdmin()
    {
        return ( $this->appState->getAreaCode() == 'adminhtml' );
    }

}
