<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Order\Transaction\Order;

class Get extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\Get\AbstractGet
{

    public function getName()
    {
        return 'Order GET';
    }

    protected function _getRequestModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Order\Message\Order\Get\Request';
    }

    protected function _getResponseModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Order\Message\Order\Get\Response';
    }
}
