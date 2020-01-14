<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Accounting\Message\Nominal\Code\Get;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Get\AbstractGet
{
    public function getFunction()
    {
        return $this->getMethod() . " accounting-service/nominal-code";
    }

    public function getAction()
    {
        return sprintf('/public-api/%s/accounting-service/nominal-code/%s',
                       $this->getTransaction()->getAccountCode(),
                       $this->getTransaction()->geIdSet());
    }
}