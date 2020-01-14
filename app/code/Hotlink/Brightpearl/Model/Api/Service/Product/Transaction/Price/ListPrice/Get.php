<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Product\Transaction\Price\ListPrice;

class Get extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\Get\AbstractGet
{

    public function getName()
    {
        return 'Price List GET';
    }

    protected function _getRequestModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Product\Message\Price\ListPrice\Get\Request';
    }

    protected function _getResponseModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Product\Message\Price\ListPrice\Get\Response';
    }

}