<?php
namespace Hotlink\Framework\Console;

abstract class AbstractCommandList implements \Magento\Framework\Console\CommandListInterface
{

    abstract public function getCommandsClasses();

    private $objectManager;

    public function __construct( \Magento\Framework\ObjectManagerInterface $objectManager )
    {
        $this->objectManager = $objectManager;
    }

    public function getCommands()
    {
        $commands = [];
        foreach ( $this->getCommandsClasses() as $class )
            {
                if ( class_exists( $class ) )
                    {
                        try
                            {
                                $commands[] = $this->objectManager->get( $class );
                            }
                        catch ( \Exception $e )
                            {
                                // unable to instantiate command
                                // could be old magento version with incompatible object manager
                                // Eg. Missing required argument $name
                                //     of Hotlink\Brightpearl\Console\Command\MSI\BulkImport
                            }
                    }
                else
                    {
                        throw new \Exception( 'Class ' . $class . ' does not exist' );
                    }
            }
        return $commands;
    }

}
