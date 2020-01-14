<?php
namespace Hotlink\Framework\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestGroupsCommand extends AbstractXdebugCommand
{

    const COMMAND_NAME = 'hotlink:test:groups';

    protected function configure()
    {
        $this->setName( self::COMMAND_NAME )->setDescription( 'Shows the phpunit test groups defined by the module' );
        parent::configure();
    }

    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $phpunit = $this->getPhpunitFile();
        $platforms = \Hotlink\Framework\Console\PlatformLocator::getPlatforms();
        foreach ( $platforms as $platform )
            {
                foreach ( [ 'unit', 'integration' ] as $testType )
                    {
                        if ( $path = $this->getTestsFolder( $platform->getModulePath(), $testType ) )
                            {
                                $name = $platform->getName();
                                $code = $platform->getCode();
                                $heading = "$name ($code) $testType tests";
                                $border = str_pad( "", strlen( $heading ) + 4, '-' );
                                echo "\n";
                                echo "$border\n";
                                echo "  $heading  \n";
                                echo "$border\n";
                                $command = "php $phpunit";
                                $command .= " --list-groups";
                                $command .= " $path";
                                $command = $this->makeBashDebugCommand( $command );
                                passthru( $command, $returnVal );
                            }
                    }
            }
    }

}
