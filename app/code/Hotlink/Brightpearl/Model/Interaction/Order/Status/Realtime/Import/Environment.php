<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Status\Realtime\Import;

class Environment extends \Hotlink\Brightpearl\Model\Interaction\Environment\AbstractEnvironment
{
    protected function _getParameterModels()
    {
        return array( 'Hotlink\Brightpearl\Model\Interaction\Order\Status\Realtime\Import\Environment\Order',
                      'Hotlink\Brightpearl\Model\Interaction\Order\Status\Realtime\Import\Environment\NotifyCustomer' );
    }
}
