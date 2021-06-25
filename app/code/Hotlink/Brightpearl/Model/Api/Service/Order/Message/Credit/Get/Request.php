<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Order\Message\Credit\Get;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Get\AbstractGet
{

    public function getFunction()
    {
        return $this->getMethod(). " order-service/sales-credit";
    }

    public function getAction()
    {
        return sprintf('/public-api/%s/order-service/sales-credit/%s',
                       $this->getTransaction()->getAccountCode(),
                       $this->getTransaction()->geIdSet());
    }

}