<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Product\Transaction\Channel;

class Get extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\Get\AbstractGet
{

    function getName()
    {
        return 'Price Channel GET';
    }

    protected function _getRequestModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Product\Message\Channel\Get\Request';
    }

    protected function _getResponseModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Product\Message\Channel\Get\Response';
    }
}