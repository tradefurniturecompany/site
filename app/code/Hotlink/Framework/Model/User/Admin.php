<?php
namespace Hotlink\Framework\Model\User;

class Admin extends \Hotlink\Framework\Model\User\AbstractUser
{

    function __construct( \Magento\Backend\Model\Auth\Session $backendAuthSession )
    {
        if ( $user = $backendAuthSession->getUser() )
            {
                $uname = $user->getUsername();
                $first = $user->getFirstname();
                $last  = $user->getLastname();

                $this->_username    = $uname;
                $this->_fullname    = "$first $last";
                $this->_description = "$first $last @ " . $this->getIP();
                $this->_name        = "$first $last";
            }
        else
            {
                $this->_username    = 'unknown';
                $this->_fullname    = 'n/a';
                $this->_description = 'n/a @ ' . $this->getIP();
                $this->_name        = 'n/a';
            }
    }

    function getType()
    {
        return "admin";
    }

}
