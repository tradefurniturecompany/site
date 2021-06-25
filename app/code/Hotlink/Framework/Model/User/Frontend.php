<?php
namespace Hotlink\Framework\Model\User;

class Frontend extends \Hotlink\Framework\Model\User\AbstractUser
{

    protected $_type;

    function __construct( \Magento\Customer\Model\Session $customerSession )
    {
        if ( $customerSession->getId() )
            {
                $user  = $customerSession->getCustomer();
                $email = $user->getEmail();
                $first = $user->getFirstname();
                $last  = $user->getLastname();

                $this->_type        = 'customer';
                $this->_username    = $email;
                $this->_fullname    = "$first $last";
                $this->_description = "$first $last"  . ' [' . $email . '] @ ' . $this->getIP();
            }
        else
            {
                $this->_type        = 'guest';
                $this->_username    = '';
                $this->_fullname    = 'guest';
                $this->_description = 'guest @ ' . $this->getIP();
            }
    }

    function getType()
    {
        return $this->_type;
    }

}
