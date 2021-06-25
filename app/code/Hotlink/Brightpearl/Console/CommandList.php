<?php
namespace Hotlink\Brightpearl\Console;

class CommandList extends \Hotlink\Framework\Console\AbstractCommandList
{

    public function getCommandsClasses()
    {
        return
            [
                \Hotlink\Brightpearl\Console\Command\Stock\BulkImport::class,
                \Hotlink\Brightpearl\Console\Command\Shipment\BulkImport::class,
                \Hotlink\Brightpearl\Console\Command\Shipment\SpecificImport::class
            ];
    }

}
