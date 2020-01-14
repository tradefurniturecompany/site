<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Warehouse\Message\Shipping\Method\Get;

class Response extends \Hotlink\Brightpearl\Model\Api\Service\Message\Response\AbstractResponse
{
    public function getMethods()
    {
        return $this->_get( 'response' );
    }
}
