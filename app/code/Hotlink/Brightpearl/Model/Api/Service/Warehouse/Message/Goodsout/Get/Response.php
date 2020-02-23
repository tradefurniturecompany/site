<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Warehouse\Message\Goodsout\Get;

class Response extends \Hotlink\Brightpearl\Model\Api\Service\Message\Response\AbstractResponse
{
    function getNotes()
    {
        return $this->_get( 'response' );
    }
}