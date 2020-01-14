<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Product\Message\Product\Get;

class Response extends \Hotlink\Brightpearl\Model\Api\Service\Message\Response\AbstractResponse
{
    public function getProducts()
    {
        return $this->_get('response');
    }
}