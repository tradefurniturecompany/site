<?php
namespace Hotlink\Framework\Console;

use Magento\Framework\ObjectManagerInterface;

class CommandList implements \Magento\Framework\Console\CommandListInterface
{

    private $objectManager;

    public function __construct( ObjectManagerInterface $objectManager )
    {
        $this->objectManager = $objectManager;
    }

    protected function getCommandsClasses()
    {
        return [
            'Hotlink\Framework\Console\Command\TestGroupsCommand',
            'Hotlink\Framework\Console\Command\TestRunCommand',
        ];
    }

    public function getCommands()
    {
        $commands = [];
        foreach ($this->getCommandsClasses() as $class) {
            if (class_exists($class)) {
                $commands[] = $this->objectManager->get($class);
            } else {
                throw new \Exception('Class ' . $class . ' does not exist');
            }
        }
        return $commands;
    }

}
