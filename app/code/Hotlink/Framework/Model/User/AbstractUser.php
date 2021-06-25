<?php
namespace Hotlink\Framework\Model\User;

abstract class AbstractUser
{

    protected $_username = false;
    protected $_fullname = false;
    protected $_name = false;
    protected $_description = false;

    abstract function getType();

    function getIP()
    {
        return ( isset( $_SERVER[ "REMOTE_ADDR" ] ) ) ? $_SERVER[ "REMOTE_ADDR" ] : 'mystery';
    }

    function getUsername()
    {
        return $this->_username;
    }

    function getFullname()
    {
        return $this->_fullname;
    }

    function getName()
    {
        return $this->_name;
    }

    function getDescription()
    {
        return $this->_description;
    }

}
