<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Order\Message\Order\Get;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Get\AbstractGet
{
    function getFunction()
    {
        return $this->getMethod(). " order-service/order";
    }

    function getAction()
    {
        return sprintf('/public-api/%s/order-service/order/%s',
                       $this->getTransaction()->getAccountCode(),
                       $this->getTransaction()->geIdSet());
    }
}