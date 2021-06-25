<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Warehouse\Transaction\Shipping;

class Get extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\Get\AbstractGet
{
    function getName()
    {
        return 'Shipping Method GET';
    }

    protected function _getRequestModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Warehouse\Message\Shipping\Method\Get\Request';
    }

    protected function _getResponseModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Warehouse\Message\Shipping\Method\Get\Response';
    }
}