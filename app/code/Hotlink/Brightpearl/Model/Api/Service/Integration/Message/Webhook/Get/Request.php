<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Integration\Message\Webhook\Get;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Get\AbstractGet
{
    public function getFunction()
    {
        return $this->getMethod() . " integration-service/webhook";
    }

    public function getAction()
    {
        return sprintf( '/public-api/%s/integration-service/webhook/%s',
                       $this->getTransaction()->getAccountCode(),
                       $this->getTransaction()->geIdSet() );
    }
}