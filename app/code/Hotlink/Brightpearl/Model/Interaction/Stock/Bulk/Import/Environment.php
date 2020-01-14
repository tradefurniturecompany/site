<?php
namespace Hotlink\Brightpearl\Model\Interaction\Stock\Bulk\Import;

class Environment extends \Hotlink\Brightpearl\Model\Interaction\Environment\AbstractEnvironment
{

    protected function _getParameterModels()
    {
        return [ '\Hotlink\Brightpearl\Model\Interaction\Stock\Bulk\Import\Environment\Batch',
                 '\Hotlink\Brightpearl\Model\Interaction\Stock\Bulk\Import\Environment\Sleep',
                 '\Hotlink\Brightpearl\Model\Interaction\Stock\Bulk\Import\Environment\Limit' ];
    }

}
