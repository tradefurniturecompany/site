<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Order\Message\Order\Get;

class Response extends \Hotlink\Brightpearl\Model\Api\Service\Message\Response\AbstractResponse
{
    // DEPRECATED, use getOrders instead
    public function getOrder()
    {
        $orderArray = $this->_get('response');
        return $orderArray[0];
    }

    public function getOrders()
    {
        return $this->_get('response');
    }
}