<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Product\Message\Channel\Get;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Get\AbstractGet
{
    function getFunction()
    {
        return $this->getMethod(). " product-service/channel";
    }

    function getAction()
    {
        return sprintf('/public-api/%s/product-service/channel/%s',
                       $this->getTransaction()->getAccountCode(),
                       $this->getTransaction()->geIdSet());
    }
}