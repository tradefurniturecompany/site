<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Product\Transaction\Product;

class Get extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\Get\AbstractGet
{

    public function getName()
    {
        return 'Product GET';
    }

    protected function _getRequestModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Product\Message\Product\Get\Request';
    }

    protected function _getResponseModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Product\Message\Product\Get\Response';
    }

}
