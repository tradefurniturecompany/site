<?php
namespace Hotlink\Brightpearl\Model\Interaction\Shipment\Bulk\Import;

class Environment extends \Hotlink\Brightpearl\Model\Interaction\Environment\AbstractEnvironment
{

    protected function _getParameterModels()
    {
        return [ '\Hotlink\Brightpearl\Model\Interaction\Shipment\Bulk\Import\Environment\Lookbehind',
                 '\Hotlink\Brightpearl\Model\Interaction\Shipment\Bulk\Import\Environment\Batch',
                 '\Hotlink\Brightpearl\Model\Interaction\Shipment\Bulk\Import\Environment\Sleep' ];
    }

}
