<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Warehouse\Message\Goodsout\Get;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Get\AbstractGet
{
    function getFunction()
    {
        return $this->getMethod(). " warehouse-service/order/goods-note";
    }

    function getAction()
    {
        return sprintf( '/public-api/%s/warehouse-service/order/%s/goods-note/goods-out/%s',
                        $this->getTransaction()->getAccountCode(),
                        $this->getTransaction()->getOrderIdSet(),
                        $this->getTransaction()->geIdSet() );
    }
}