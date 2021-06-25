<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Warehouse\Transaction\Warehouse;

class Get extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\Get\AbstractGet
{

    function getName()
    {
        return 'Warehouse GET';
    }

    protected function _getRequestModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Warehouse\Message\Warehouse\Get\Request';
    }

    protected function _getResponseModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Warehouse\Message\Warehouse\Get\Response';
    }
}