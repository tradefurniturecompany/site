<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Product\Message\Price\ListPrice\Get;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Get\AbstractGet
{
    public function getFunction()
    {
        return $this->getMethod(). " product-service/price-list";
    }

    public function getAction()
    {
        return sprintf('/public-api/%s/product-service/price-list/%s',
                       $this->getTransaction()->getAccountCode(),
                       $this->getTransaction()->geIdSet());
    }
}