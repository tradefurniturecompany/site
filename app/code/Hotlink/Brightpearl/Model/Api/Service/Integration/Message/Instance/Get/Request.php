<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Integration\Message\Instance\Get;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Get\AbstractGet
{

    public function getFunction()
    {
        return $this->getMethod() . " integration-service/integration";
    }

    public function getAction()
    {
        return sprintf( '/public-api/%s/integration-service/integration/instance', $this->getTransaction()->getAccountCode() );
    }

}