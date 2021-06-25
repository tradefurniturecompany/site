<?php
namespace Hotlink\Brightpearl\Model\Exception;

class Api extends \Hotlink\Brightpearl\Model\Exception\AbstractException
{
    protected $_statusCode = null;

    function setStatusCode( $code )
    {
        $this->_statusCode = $code;
        return $this;
    }

    function getStatusCode()
    {
        return $this->_statusCode;
    }

}