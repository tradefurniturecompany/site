<?php
namespace Hotlink\Framework\Console;

class CommandList extends \Hotlink\Framework\Console\AbstractCommandList
{

    public function getCommandsClasses()
    {
        return
            [
                \Hotlink\Framework\Console\Command\LogClean::class,
                \Hotlink\Framework\Console\Command\TestGroupsCommand::class,
                \Hotlink\Framework\Console\Command\TestRunCommand::class
            ];
    }

}
