<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Order\Message\Field\Metadata\Get;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Get\AbstractGet
{
    function getFunction()
    {
        return $this->getMethod(). " order-service/custom-field-meta-data";
    }

    function getAction()
    {
        return sprintf('/public-api/%s/order-service/%s/custom-field-meta-data/%s',
                       $this->getTransaction()->getAccountCode(),
                       $this->getTransaction()->getSource(),
                       $this->getTransaction()->geIdSet());
    }
}