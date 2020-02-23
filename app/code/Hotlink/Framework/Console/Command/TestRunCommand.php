<?php
namespace Hotlink\Framework\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestRunCommand extends AbstractXdebugCommand
{

    const INPUT_ARG_TYPE = 'type';
    const INPUT_ARG_PATH = 'path';

    const INPUT_OPTION_QUICK = 'quick';
    const INPUT_OPTION_QUICK_SHORTCUT = null;

    const INPUT_OPTION_OUTPUT_PRINTER = 'output';
    const INPUT_OPTION_OUTPUT_PRINTER_SHORTCUT = 'o';

    const INPUT_OPTION_FILTER_INCLUDE_GROUPS = 'groups';
    const INPUT_OPTION_FILTER_INCLUDE_GROUPS_SHORTCUT = 'g';

    const INPUT_OPTION_PLATFORM = 'platform';
    const INPUT_OPTION_PLATFORM_SHORTCUT = 'p';

    const INPUT_OPTION_FILTER_EXCLUDE_GROUPS = 'exclude';
    const INPUT_OPTION_FILTER_EXCLUDE_GROUPS_SHORTCUT = 'x';

    const COMMAND_NAME = 'hotlink:test:run';

    protected $types = [ 'all', 'unit', 'integration' ];

    protected function configure()
    {
        $this->setName( self::COMMAND_NAME )->setDescription( 'Runs platform tests' );

        $this->addArgument( self::INPUT_ARG_TYPE,
                            InputArgument::OPTIONAL,
                            'Type of test to run. Available types: ' . implode( ', ', $this->types ),
                            'all'
                            );

        $this->addArgument( self::INPUT_ARG_PATH,
                            InputArgument::OPTIONAL,
                            "Only runs test under the given subfolder (relative path to tests)",
                            ''
                            );
        
        $this->addOption( self::INPUT_OPTION_QUICK,
                          self::INPUT_OPTION_QUICK_SHORTCUT,
                          null,
                          "Skip the complete reinstall of the integration test environment"
                          );

        $this->addOption(
                           self::INPUT_OPTION_OUTPUT_PRINTER,
                           self::INPUT_OPTION_OUTPUT_PRINTER_SHORTCUT,
                           null,
                           'Disables the bash formatted report and reverts to standard phpunit output'
                         );

        $codes = [];
        foreach ( \Hotlink\Framework\Console\PlatformLocator::getPlatforms() as $platform )
            {
                $codes[] = $platform->getCode();
            }
        $this->addOption(
                           self::INPUT_OPTION_PLATFORM,
                           self::INPUT_OPTION_PLATFORM_SHORTCUT,
                           InputOption::VALUE_OPTIONAL,
                           'Platform to run tests for: ' . implode( ', ', $codes ),
                           $codes[ 0 ]
                         );

        $this->addOption(
                           self::INPUT_OPTION_FILTER_INCLUDE_GROUPS,
                           self::INPUT_OPTION_FILTER_INCLUDE_GROUPS_SHORTCUT,
                           InputOption::VALUE_OPTIONAL,
                           'Includes the named groups (csv) in the test run',
                           false
                         );

        $this->addOption(
                           self::INPUT_OPTION_FILTER_EXCLUDE_GROUPS,
                           self::INPUT_OPTION_FILTER_EXCLUDE_GROUPS_SHORTCUT,
                           InputOption::VALUE_OPTIONAL,
                           'Excludes the named groups (csv) from the test run',
                           false
                         );

        parent::configure();
    }

    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $started = $this->microtime_float();
        $argType = $input->getArgument( self::INPUT_ARG_TYPE );
        $argPlatform = $input->getOption( self::INPUT_OPTION_PLATFORM );

        if ( $platform = \Hotlink\Framework\Console\PlatformLocator::getPlatform( $argPlatform ) )
            {
                switch ( $argType )
                    {
                        case 'unit':
                            $result = $this->runUnitTests( $platform, $input, $output );
                            break;
                        case 'integration':
                            $result = $this->runIntegrationTests( $platform, $input, $output );
                            break;
                        case 'all':
                            $result = $this->runUnitTests( $platform, $input, $output );
                            $result = $result + $this->runIntegrationTests( $platform, $input, $output );
                            break;
                    }
            }
        $finished = $this->microtime_float();
        $seconds = $finished - $started;
        $reasonable = number_format( $seconds, 4 );

        $output->writeln( " Test run completed in $reasonable seconds" );
        $output->writeln( '' );

        return 0;
    }

    protected function getPhpunitCommandWithOptions( InputInterface $input )
    {
        $phpunit = $this->getPhpunitFile();
        $command = "php $phpunit";
        $command = $this->addOptionalIncludeGroup( $input, $command );
        $command = $this->addOptionalExcludeGroup( $input, $command );
        $command = $this->addOptionalPrinter( $input, $command );
        return $command;
    }

    protected function runUnitTests( \Hotlink\Framework\Model\Platform\AbstractPlatform $platform, InputInterface $input, OutputInterface $output )
    {
        $returnVal = true;
        $title = $platform->getName() . " (" . $platform->getCode() . ")";
        if ( $path = $this->getTestsFolder( $platform->getModulePath(), 'unit' ) )
            {
                $bootstrap = $path . '/bootstrap.php';
                $phpunitxml = $path . '/phpunit.xml';

                $this->writeDividor( $output, " Unit Tests Start : $title" );

                $command = $this->getPhpunitCommandWithOptions( $input );
                $command .= " --bootstrap $bootstrap";
                $command .= " $path";

                $command = $this->makeBashDebugCommand( $command );
                $returnVal = false;
                passthru( $command, $returnVal );

                $this->writeDividor( $output, " Unit Tests End : $title" );
            }
        else
            {
                $this->writeDividor( $output, " >>> No unit tests defined : $title" );
            }
        return $returnVal;
    }

    /*

      phpunit bug

      configuration and bootstrap processing order should not matter, however it does.
      bootstrap.php depends on constants defined within the configuration file phpunit.xml.

      when the configuration file is processed first, all works ok.
      when the bootstrap runs first, exceptions and failure.

      defining boostrap filename within configration xml forces config xml to load first (success).
      using the --boostrap parameter of phpunit causes bootstrap file to load before config (exception).

      Therefore --bootstrap command line parameter is unreliable/inconsistent - do not use.

      $boostrap = realpath( './dev/tests/integration/framework/bootstrap.php' );
      $command .= " --bootstrap $bootstrap";

    */
    protected function runIntegrationTests( \Hotlink\Framework\Model\Platform\AbstractPlatform $platform, InputInterface $input, OutputInterface $output )
    {
        $returnVal = true;
        $title = $platform->getName() . " (" . $platform->getCode() . ")";
        if ( $path = $this->getTestsFolder( $platform->getModulePath(), 'integration' ) )
            {
                $phpunitxml = $path . '/phpunit.xml';
                if ( $this->isQuickRun( $input ) )
                    {
                        $phpunitxml = $path . '/phpunit.quick.xml';
                    }

                $this->writeDividor( $output, " Integration Tests Start : $title" );

                $command = $this->getPhpunitCommandWithOptions( $input );
                $command .= " --configuration " . $phpunitxml;
                $command .= " $path";
                $command = $this->makeBashDebugCommand( $command );

                $returnVal = false;
                passthru( $command, $returnVal );

                $this->writeDividor( $output, " Integration Tests End : $title" );
            }
        else
            {
                $this->writeDividor( $output, " >>> No integration tests defined  : $title" );
            }
        return $returnVal;
    }

    protected function writeDividor( OutputInterface $output, $name )
    {
        $output->writeln( '' );
        $output->writeln( str_repeat( '-', 70 ) );
        $output->writeln( $name );
        $output->writeln( str_repeat( '-', 70 ) );
        $output->writeln( '' );
    }

    protected function addOptionalIncludeGroup( InputInterface $input, $command )
    {
        if ( $option = $input->getOption( self::INPUT_OPTION_FILTER_INCLUDE_GROUPS ) )
            {
                $command .= ' --group ' . $option;
            }
        return $command;
    }

    protected function addOptionalExcludeGroup( InputInterface $input, $command )
    {
        if ( $option = $input->getOption( self::INPUT_OPTION_FILTER_EXCLUDE_GROUPS ) )
            {
                $command .= ' --exclude-group ' . $option;
            }
        return $command;
    }

    protected function isQuickRun( InputInterface $input )
    {
        if ( $option = $input->getOption( self::INPUT_OPTION_QUICK ) )
            {
                return true;
            }
        return false;
    }

    protected function addOptionalPrinter( InputInterface $input, $command )
    {
        if ( ! ( $option = $input->getOption( self::INPUT_OPTION_OUTPUT_PRINTER ) ) )
            {
                if ( $platform = \Hotlink\Framework\Console\PlatformLocator::getPlatform( 'hotlink' ) )
                    {
                        $tests = $this->getTestsFolder( $platform->getModulePath() );
                        $command .= " --printer 'Hotlink\Framework\Test\BashPrinter'";
                    }
            }
        return $command;
    }

    //
    //  returns the current timestamp in millionths of a second
    //
    function microtime_float()
    {
        list( $usec, $sec ) = explode( " ", microtime() );
        return ( ( float ) $usec + ( float ) $sec );
    }

}
