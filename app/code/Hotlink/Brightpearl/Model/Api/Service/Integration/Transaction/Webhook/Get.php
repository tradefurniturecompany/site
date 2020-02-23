<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Integration\Transaction\Webhook;

class Get extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\Get\AbstractGet
{
    function getName()
    {
        return 'Webhook GET';
    }

    protected function _getRequestModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Integration\Message\Webhook\Get\Request';
    }

    protected function _getResponseModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Integration\Message\Webhook\Get\Response';
    }
}