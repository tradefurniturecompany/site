<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Warehouse\Message\Warehouse\Location\Quarantine\Get;

class Response extends \Hotlink\Brightpearl\Model\Api\Service\Message\Response\AbstractResponse
{

    public function getLocation()
    {
        return $this->_get( 'response' );
    }

}
