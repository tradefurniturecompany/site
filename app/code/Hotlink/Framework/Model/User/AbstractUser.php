<?php
namespace Hotlink\Framework\Model\User;

abstract class AbstractUser
{

    protected $_username = false;
    protected $_fullname = false;
    protected $_name = false;
    protected $_description = false;

    abstract public function getType();

    public function getIP()
    {
        return ( isset( $_SERVER[ "REMOTE_ADDR" ] ) ) ? $_SERVER[ "REMOTE_ADDR" ] : 'mystery';
    }

    public function getUsername()
    {
        return $this->_username;
    }

    public function getFullname()
    {
        return $this->_fullname;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getDescription()
    {
        return $this->_description;
    }

}
