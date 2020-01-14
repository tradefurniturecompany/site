<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Warehouse\Message\Warehouse\Get;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Get\AbstractGet
{
    public function getFunction()
    {
        return $this->getMethod(). " warehouse-service/warehouse";
    }

    public function getAction()
    {
        return sprintf( '/public-api/%s/warehouse-service/warehouse/%s',
                        $this->getTransaction()->getAccountCode(),
                        $this->getTransaction()->geIdSet() );
    }
}
