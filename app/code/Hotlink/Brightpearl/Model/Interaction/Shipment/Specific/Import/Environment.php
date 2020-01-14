<?php
namespace Hotlink\Brightpearl\Model\Interaction\Shipment\Specific\Import;

class Environment extends \Hotlink\Brightpearl\Model\Interaction\Environment\AbstractEnvironment
{

    protected function _getParameterModels()
    {
        return [ '\Hotlink\Brightpearl\Model\Interaction\Shipment\Specific\Import\Environment\Noteid',
                 '\Hotlink\Brightpearl\Model\Interaction\Shipment\Specific\Import\Environment\Notetype',
                 '\Hotlink\Brightpearl\Model\Interaction\Shipment\Specific\Import\Environment\Notify' ];
    }

}
