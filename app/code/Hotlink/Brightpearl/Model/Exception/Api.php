<?php
namespace Hotlink\Brightpearl\Model\Exception;

class Api extends \Hotlink\Brightpearl\Model\Exception\AbstractException
{
    protected $_statusCode = null;

    public function setStatusCode( $code )
    {
        $this->_statusCode = $code;
        return $this;
    }

    public function getStatusCode()
    {
        return $this->_statusCode;
    }

}