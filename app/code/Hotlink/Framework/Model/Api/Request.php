<?php
namespace Hotlink\Framework\Model\Api;

class Request extends \Hotlink\Framework\Model\Api\Message\AbstractMessage
{

    protected $_function = false;

    function getFunction()
    {
        return $this->_function;
    }

    function setFunction( $value )
    {
        $this->_function = $value;
        return $this;
    }

}
