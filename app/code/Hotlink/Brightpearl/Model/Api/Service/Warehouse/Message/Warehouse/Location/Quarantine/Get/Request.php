<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Warehouse\Message\Warehouse\Location\Quarantine\Get;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Get\AbstractGet
{

    function getFunction()
    {
        return $this->getMethod(). " warehouse-service/warehouse/{id}/location/quarantine";
    }

    function getAction()
    {
        return sprintf( '/public-api/%s/warehouse-service/warehouse/%s/location/quarantine',
                        $this->getTransaction()->getAccountCode(),
                        $this->getTransaction()->geIdSet()
        );
    }

}
