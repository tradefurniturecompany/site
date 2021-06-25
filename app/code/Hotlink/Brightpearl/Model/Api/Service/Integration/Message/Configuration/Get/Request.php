<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Integration\Message\Configuration\Get;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Get\AbstractGet
{

    public function getFunction()
    {
        return $this->getMethod() . " integration-service/account-configuration";
    }

    public function getAction()
    {
        return sprintf( '/public-api/%s/integration-service/account-configuration', $this->getTransaction()->getAccountCode() );
    }

}