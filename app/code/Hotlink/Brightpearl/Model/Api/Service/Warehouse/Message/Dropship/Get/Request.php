<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Warehouse\Message\Dropship\Get;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Get\AbstractGet
{
    public function getFunction()
    {
        return $this->getMethod(). " warehouse-service/shipping-method";
    }

    public function getAction()
    {
        return sprintf( '/public-api/%s/warehouse-service/order/%s/goods-note/drop-ship/%s',
                        $this->getTransaction()->getAccountCode(),
                        $this->getTransaction()->getOrderIdSet(),
                        $this->getTransaction()->geIdSet() );
    }
}