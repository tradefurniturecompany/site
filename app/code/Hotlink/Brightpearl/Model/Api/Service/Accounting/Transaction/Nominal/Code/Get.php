<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Accounting\Transaction\Nominal\Code;

class Get extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\Get\AbstractGet
{

    function getName()
    {
        return 'Nominal Code GET';
    }

    protected function _getRequestModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Accounting\Message\Nominal\Code\Get\Request';
    }

    protected function _getResponseModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Accounting\Message\Nominal\Code\Get\Response';
    }
}