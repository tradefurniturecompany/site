<?php
namespace Hotlink\Framework\Model\Api;

class Request extends \Hotlink\Framework\Model\Api\Message\AbstractMessage
{

    protected $_function = false;

    public function getFunction()
    {
        return $this->_function;
    }

    public function setFunction( $value )
    {
        $this->_function = $value;
        return $this;
    }

}
