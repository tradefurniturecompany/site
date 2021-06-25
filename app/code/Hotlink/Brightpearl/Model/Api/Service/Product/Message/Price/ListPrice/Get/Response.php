<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Product\Message\Price\ListPrice\Get;

class Response extends \Hotlink\Brightpearl\Model\Api\Service\Message\Response\AbstractResponse
{
    function getPriceLists()
    {
        return $this->_get('response');
    }
}