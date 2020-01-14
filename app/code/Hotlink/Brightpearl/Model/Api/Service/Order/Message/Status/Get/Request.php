<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Order\Message\Status\Get;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Get\AbstractGet
{
    public function getFunction()
    {
        return $this->getMethod(). " order-service/order-status";
    }

    public function getAction()
    {
        return sprintf('/public-api/%s/order-service/order-status/%s',
                       $this->getTransaction()->getAccountCode(),
                       $this->getTransaction()->geIdSet());
    }
}