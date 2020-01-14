<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Integration\Transaction\Instance;

class Get extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\Get\AbstractGet
{

    public function getName()
    {
        return 'Integration Instance GET';
    }

    protected function _getRequestModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Integration\Message\Instance\Get\Request';
    }

    protected function _getResponseModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Integration\Message\Instance\Get\Response';
    }

}