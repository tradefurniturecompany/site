<?php
namespace Hotlink\Framework\Model;

class User
{

    protected $_username = false;
    protected $_fullname = false;

    protected $storeManager;
    protected $backendAuthSession;
    protected $customerSession;
    protected $scopeHelper;

    function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\Customer\Model\Session $customerSession,
        \Hotlink\Framework\Helper\Scope $scopeHelper
    ) {
        $this->storeManager = $storeManager;
        $this->backendAuthSession = $backendAuthSession;
        $this->customerSession = $customerSession;
        $this->scopeHelper = $scopeHelper;
    }

    function getDescription()
    {
        $fullname = $this->getFullname();
        switch ( $this->getType() )
            {
            case 'admin':
                return $fullname . ' @ ' . $this->getIP();
            case 'customer':
                return $fullname . ' [' . $this->getUsername() . '] @ ' . $this->getIP();
            case 'guest':
                return $fullname . ' @ ' . $this->getIP();
            }
        return "Unknown " . $this->getType();
    }

    protected function _initialise()
    {
        if ( $this->scopeHelper->isAdmin() )
            {
                $this->_type = "admin";
                if ( $user = $this->backendAuthSession->getUser() )
                    {
                        $this->_username = $user->getUsername();
                        $this->_fullname = $user->getFirstname() . ' ' . $user->getLastname();
                    }
                else
                    {
                        $this->_username = "unknown";
                        $this->_fullname = 'n/a';
                    }
            }
        else
            {
                if ( $this->customerSession->getId() )
                    {
                        $this->_type = "customer";
                        $user = $this->customerSession->getCustomer();
                        $this->_username = $user->getEmail();
                        $this->_fullname = $user->getFirstname() . ' ' . $user->getLastname();
                    }
                else
                    {
                        $this->_type = "guest";
                        $this->_username = "";
                        $this->_fullname = 'guest';
                    }
            }
    }

    function getIP()
    {
        return ( isset( $_SERVER[ "REMOTE_ADDR" ] ) ) ? $_SERVER[ "REMOTE_ADDR" ] : 'mystery';
    }

    function getType()
    {
        if ( ! $this->_type )
            {
                $this->_initialise();
            }
        return $this->_type;
    }

    function getUsername()
    {
        if ( ! $this->_username )
            {
                $this->_initialise();
            }
        return $this->_username;
    }

    function getFullname()
    {
        if ( ! $this->_fullname )
            {
                $this->_initialise();
            }
        return $this->_fullname;
    }

    function getName()
    {
        return $this->getFullname();
    }

}
