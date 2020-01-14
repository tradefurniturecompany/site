<?php
namespace Hotlink\Brightpearl\Model\Interaction\Stock\Realtime\Import;

class Environment extends \Hotlink\Brightpearl\Model\Interaction\Environment\AbstractEnvironment
{

    protected function _getParameterModels()
    {
        return [ '\Hotlink\Framework\Model\Interaction\Environment\Parameter\Stream\Reader',
                 '\Hotlink\Brightpearl\Model\Interaction\Stock\Realtime\Import\Environment\Filter',
                 '\Hotlink\Brightpearl\Model\Interaction\Stock\Realtime\Import\Environment\Warehouse',
                 '\Hotlink\Brightpearl\Model\Interaction\Stock\Realtime\Import\Environment\Index',
                 '\Hotlink\Brightpearl\Model\Interaction\Stock\Realtime\Import\Environment\Unmanaged',
                 '\Hotlink\Brightpearl\Model\Interaction\Stock\Realtime\Import\Environment\Instock',
                 '\Hotlink\Brightpearl\Model\Interaction\Stock\Realtime\Import\Environment\Zeroqty',
                 '\Hotlink\Brightpearl\Model\Interaction\Stock\Realtime\Import\Environment\Ttl',
                 '\Hotlink\Brightpearl\Model\Interaction\Stock\Realtime\Import\Environment\Limit' ];
    }

}