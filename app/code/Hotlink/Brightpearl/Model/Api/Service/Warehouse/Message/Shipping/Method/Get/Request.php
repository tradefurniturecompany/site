<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Warehouse\Message\Shipping\Method\Get;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Get\AbstractGet
{
    function getFunction()
    {
        return $this->getMethod(). " warehouse-service/shipping-method";
    }

    function getAction()
    {
        return sprintf( '/public-api/%s/warehouse-service/shipping-method/%s',
                        $this->getTransaction()->getAccountCode(),
                        $this->getTransaction()->geIdSet() );
    }
}
