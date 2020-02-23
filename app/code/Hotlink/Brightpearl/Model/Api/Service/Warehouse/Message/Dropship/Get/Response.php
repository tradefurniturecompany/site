<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Warehouse\Message\Dropship\Get;

class Response extends \Hotlink\Brightpearl\Model\Api\Service\Message\Response\AbstractResponse
{
    function getNotes()
    {
        return $this->_get( 'response' );
    }
}