<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Reconciliation;

class Environment extends \Hotlink\Brightpearl\Model\Interaction\Environment\AbstractEnvironment
{
    protected function _getParameterModels()
    {
        return [
            '\Hotlink\Brightpearl\Model\Interaction\Order\Reconciliation\Environment\Batch',
            '\Hotlink\Brightpearl\Model\Interaction\Order\Reconciliation\Environment\Sleep',
            '\Hotlink\Brightpearl\Model\Interaction\Order\Reconciliation\Environment\Ignore',
            '\Hotlink\Brightpearl\Model\Interaction\Order\Reconciliation\Environment\Errors',
            '\Hotlink\Brightpearl\Model\Interaction\Order\Reconciliation\Environment\Startdate'
            ];
    }
}
