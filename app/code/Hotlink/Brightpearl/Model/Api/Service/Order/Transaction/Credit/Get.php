<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Order\Transaction\Credit;

class Get extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\Get\AbstractGet
{

    public function getName()
    {
        return 'Sales Credit GET';
    }

    protected function _getRequestModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Order\Message\Credit\Get\Request';
    }

    protected function _getResponseModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Order\Message\Credit\Get\Response';
    }

}
