<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Status\Bulk\Import;

class Environment extends \Hotlink\Brightpearl\Model\Interaction\Environment\AbstractEnvironment
{
    protected function _getParameterModels()
    {
        return [ 'Hotlink\Brightpearl\Model\Interaction\Order\Status\Bulk\Import\Environment\Lookbehind',
                 'Hotlink\Brightpearl\Model\Interaction\Order\Status\Bulk\Import\Environment\Batch',
                 'Hotlink\Brightpearl\Model\Interaction\Order\Status\Bulk\Import\Environment\Sleep',
                 'Hotlink\Brightpearl\Model\Interaction\Order\Status\Bulk\Import\Environment\NotifyCustomer' ];
    }
}
