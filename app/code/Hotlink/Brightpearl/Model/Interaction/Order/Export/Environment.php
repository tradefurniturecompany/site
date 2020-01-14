<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Export;

class Environment extends \Hotlink\Brightpearl\Model\Interaction\Order\Environment\AbstractEnvironment
{
    protected function _getParameterModels()
    {
        return array( '\Hotlink\Framework\Model\Interaction\Environment\Parameter\Stream\Reader',
                      '\Hotlink\Brightpearl\Model\Interaction\Order\Environment\Parameter\Filter\Order',
                      '\Hotlink\Brightpearl\Model\Interaction\Order\Export\Environment\Force' );
    }
}