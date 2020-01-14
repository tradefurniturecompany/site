<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Integration\Transaction\Configuration;

class Get extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\Get\AbstractGet
{

    public function getName()
    {
        return 'Account Configuration GET';
    }

    protected function _getRequestModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Integration\Message\Configuration\Get\Request';
    }

    protected function _getResponseModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Integration\Message\Configuration\Get\Response';
    }

}