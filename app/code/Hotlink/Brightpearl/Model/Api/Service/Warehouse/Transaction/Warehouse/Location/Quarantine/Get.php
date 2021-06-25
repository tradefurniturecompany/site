<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Warehouse\Transaction\Warehouse\Location\Quarantine;

class Get extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\Get\AbstractGet
{

    public function getName()
    {
        return 'Warehouse Location Quarantine GET';
    }

    protected function _getRequestModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Warehouse\Message\Warehouse\Location\Quarantine\Get\Request';
    }

    protected function _getResponseModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Warehouse\Message\Warehouse\Location\Quarantine\Get\Response';
    }
}