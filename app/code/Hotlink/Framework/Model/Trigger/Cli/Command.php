<?php
namespace Hotlink\Framework\Model\Trigger\Cli;

class Command extends \Hotlink\Framework\Model\Trigger\AbstractTrigger
{

    const CONTEXT = 'initiated_from_command_line';

    const EVENT   = 'hotlink_framework_trigger_cli_command';

    /*
      Unlike most triggers, this one overloads _execute and reads the interactions from passed event data
    */
    const KEY_INTERACTION = "interaction";
    const KEY_COLOURS     = "colours";
    const KEY_LEVEL       = "report_level";

    public static function getEventConfiguration( $interaction, $colours, $level )
    {
        return
            [ self::KEY_INTERACTION => $interaction,
              self::KEY_COLOURS     => $colours,
              self::KEY_LEVEL       => $level
            ];
    }

    protected function _getName()
    {
        return 'Command line';
    }

    public function getMagentoEvents()
    {
        return [ 'Initiated by command line' => self::EVENT ];
    }

    public function getContexts()
    {
        return [ self::CONTEXT => 'Initiated manually from command line' ];
    }

    public function getContext()
    {
        return self::CONTEXT;
    }

    public function getContextLabel()
    {
        if ( isset( $_SERVER[ 'argv' ] ) && ( is_array( $_SERVER[ 'argv' ] ) ) )
            {
                $command = implode( ' ', $_SERVER[ 'argv' ] );
                return $command;
            }
        return parent::getContextLabel();
    }

    protected function _execute()
    {
        $event = $this->getMagentoEvent();
        if ( $interaction = $event[ self::KEY_INTERACTION ] )
            {
                $colours = ( isset( $event[ self::KEY_COLOURS ] ) ? $event[ self::KEY_COLOURS ] : true );
                $level   = ( isset( $event[ self::KEY_LEVEL ] ) ? $event[ self::KEY_LEVEL ] : \Hotlink\Framework\Model\Report\Item::LEVEL_TRACE );
                $interaction
                    ->setTrigger( $this )
                    ->getReport()
                    ->addStdoutWriter()
                    ->getWriter( \Hotlink\Framework\Model\Report\Stdout\Writer::CODE )
                    ->setColours( $colours )
                    ->setLevel( $level );

                $interaction->canExecute( true );  // force an exception if exists
                $interaction->execute();
            }
    }

}
