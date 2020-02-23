<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Product\Message\Product\Get;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Get\AbstractGet
{
    function getFunction()
    {
        return $this->getMethod(). " product-service/product";
    }

    function getAction()
    {
        return sprintf('/public-api/%s/product-service/product/%s',
                       $this->getTransaction()->getAccountCode(),
                       $this->getTransaction()->geIdSet());
    }
}