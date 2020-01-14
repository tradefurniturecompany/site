<?php
namespace Hotlink\Framework\Model\Config;

class Map
{

    const PLATFORM        = 'platform';
    const PLATFORMS       = 'platforms';
    const MONITORS        = 'monitors';
    const TRIGGERS        = 'triggers';
    const ACTIONS         = 'actions';
    const IMPLEMENTATIONS = 'implementations';
    const INTERACTIONS    = 'interactions';
    const DATAOBJECTS     = 'dataobjects';

    protected $_initialised = false;

    //
    //  This class uses xml keys internally to validate xml structures, but exposes all references as class names publicly.
    //
    //  Config Rules:
    //
    //  An interaction can only belong to one platform.
    //  Each platform, trigger, interaction, monitor and action xml keys must be unique.
    //  Interaction xml keys must also be unique across platforms.
    //

    protected $_config = false;

    protected $_platforms = array();
    protected $_triggers = array();
    protected $_monitors = array();
    protected $_actions = array();
    protected $_interactions = array();
    protected $_requirements = array();
    protected $_dataobjects = array();

    //
    //  These lookup provide internal mappings between xml keys and class names
    //
    protected $_keys = array();            // List of all keys
    protected $_classes = array();         // List of call classes


    protected $exceptionHelper;
    protected $reflectionHelper;

    public function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Model\Config\Map\Reader $reader
    )
    {
        $this->exceptionHelper = $exceptionHelper;
        $this->reflectionHelper = $reflectionHelper;
        //$this->_config = $this->getSingleton( 'Hotlink\Framework\Model\Config\Map\Reader' )->read();
        $this->_config = $reader->read();
        $this->_init ( $this->_config );
        //$this->setConfig( $reader->read() );
        //$this->_init();
    }

    protected function _init( $config )
    {
        $this->_platforms = [];
        $this->_interactions = [];
        $this->_triggers = [];
        $this->_monitors = [];
        $this->_actions = [];
        //$this->_requirements = [];
        //$this->_dataobjects = [];
        foreach ( $config as $platform => $interactions )
            {
                $this->_platforms[ $platform ] = [ self::INTERACTIONS => [],
                                                   self::TRIGGERS     => [],
                                                   self::MONITORS     => [],
                                                   self::ACTIONS      => [] ];
                foreach ( $interactions as $interaction => $extras )
                    {
                        $this->_platforms[ $platform ][ self::INTERACTIONS ][] = $interaction;
                        $this->_interactions[ $interaction ] = $extras;
                        $this->_interactions[ $interaction ][ self::PLATFORM ] = $platform;
                        if ( isset( $extras[ self::TRIGGERS ] ) )
                            {
                                foreach ( $extras[ self::TRIGGERS ] as $trigger )
                                    {
                                        if ( !array_key_exists( $trigger, $this->_triggers ) )
                                            {
                                                $this->_triggers[ $trigger ] = [ self::INTERACTIONS => [] ];
                                            }
                                        $this->_triggers[ $trigger ][ self::INTERACTIONS ][] = $interaction;
                                        $this->_platforms[ $platform ][ self::TRIGGERS ][] = $trigger;
                                    }
                            }
                        if ( isset( $extras[ self::MONITORS ] ) )
                            {
                                foreach ( $extras[ self::MONITORS ] as $monitor )
                                    {
                                        if ( !array_key_exists( $monitor, $this->_monitors ) )
                                            {
                                                $this->_monitors[ $monitor ] = [ self::INTERACTIONS => [] ];
                                            }
                                        $this->_monitors[ $monitor ][ self::INTERACTIONS ][] = $interaction;
                                        $this->_platforms[ $platform ][ self::MONITORS ][] = $monitor;
                                    }
                            }
                        if ( isset( $extras[ self::ACTIONS ] ) )
                            {
                                foreach ( $extras[ self::ACTIONS ] as $action )
                                    {
                                        if ( !array_key_exists( $action, $this->_actions ) )
                                            {
                                                $this->_actions[ $action ] = [ self::INTERACTIONS => [] ];
                                            }
                                        $this->_actions[ $action ][ self::INTERACTIONS ][] = $interaction;
                                        $this->_platforms[ $platform ][ self::ACTIONS ][] = $action;
                                    }
                            }
                    }
            }
    }

    public function setConfig( $config )
    {
        $this->_config = $config;
    }

    public function getSingleton( $class )
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->get( $class );
    }

    protected function _stringify( $thing )
    {
        return is_object( $thing ) ? "\\" . get_class( $thing ) : $thing;
    }

    //
    //  Public interface
    //
    public function getPlatforms()
    {
        return array_keys( $this->_platforms );
    }

    public function getPlatform( $thing = null )
    {
        $thing = $this->_stringify( $thing );
        if ( array_key_exists( $thing, $this->_interactions ) )
            {
                return $this->_interactions[ $thing ][ self::PLATFORM ];
            }
        return null;
    }

    public function getMonitors( $thing = null )
    {
        $thing = $this->_stringify( $thing );
        if ( is_null( $thing ) )
            {
                return array_keys( $this->_monitors );
            }
        if ( isset( $this->_interactions[ $thing ][ self::MONITORS ] ) )
            {
                return $this->_interactions[ $thing ][ self::MONITORS ];
            }
        return [];
    }

    public function getTriggers( $thing = null )
    {
        $thing = $this->_stringify( $thing );
        if ( is_null( $thing ) )
            {
                return array_keys( $this->_triggers );
            }
        // if ( $thing instanceof \Hotlink\Framework\Model\Interaction\AbstractInteraction )
        //     {
        //         return $this->_lookup( $thing, $this->_interactions, self::TRIGGERS );
        //     }
        // else if ( $thing instanceof \Hotlink\Framework\Model\Monitor\AbstractMonitor )
        //     {
        //         return $this->_lookup( $thing, $this->_monitors, self::TRIGGERS );
        //     }
        if ( is_string( $thing ) )
            {
                if ( isset( $this->_interactions[ $thing ][ self::TRIGGERS ] ) )
                    {
                        return $this->_interactions[ $thing ][ self::TRIGGERS ];
                    }
            }
        return [];
    }

    public function getInteractions( $thing = null )
    {
        $thing = $this->_stringify( $thing );
        if ( is_null( $thing ) )
            {
                return array_keys( $this->_interactions );
            }
        if ( array_key_exists( $thing, $this->_platforms ) )
            {
                return $this->_platforms[ $thing ][ self::INTERACTIONS ];
            }
        if ( array_key_exists( $thing, $this->_triggers ) )
            {
                return $this->_triggers[ $thing ][ self::INTERACTIONS ];
            }
        if ( array_key_exists( $thing, $this->_monitors ) )
            {
                return $this->_monitors[ $thing ][ self::INTERACTIONS ];
            }
        // if ( $thing instanceof \Hotlink\Framework\Model\Platform\AbstractPlatform )
        //     {
        //         return $this->_lookup( $thing, $this->_triggers );
        //     }
        // if ( $thing instanceof \Hotlink\Framework\Model\Trigger\AbstractTrigger )
        //     {
        //         return $this->_lookup( $thing, $this->_triggers );
        //     }
        // else if ( $thing instanceof \Hotlink\Framework\Model\Monitor\AbstractMonitor )
        //     {
        //         return $this->_lookup( $thing, $this->_monitors );
        //     }
        // else if ( $thing instanceof \Hotlink\Framework\Model\Platform\AbstractPlatform )
        //     {
        //         return $this->_lookup( $thing, $this->_platforms );
        //     }
        //$this->exception()->throwProcessing( __FUNCTION__ . ' requires a platform or trigger object parameter.', $this );

        return [];
    }

    //public function getImplementations( \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction )
    public function getImplementations( $thing = null )
    {
        $thing = $this->_stringify( $thing );
        if ( array_key_exists( $thing, $this->_interactions ) )
            {
                return $this->_interactions[ $thing ][ self::IMPLEMENTATIONS ];
            }
        return [];
    }

    public function getIndexOfInteraction( \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction )
    {
        $counter = 0;
        $found = false;
        $class = get_class( $interaction );
        foreach ( $this->_interactions as $key => $val )
            {
                $counter++;
                if ( $key == $class )
                    {
                        $found = true;
                        break;
                    }
            }
        return ( $found ) ? $counter : 0;
    }

    public function getActions( $thing = null )
    {
        $thing = $this->_stringify( $thing );
        if ( is_null( $thing ) )
            {
                return array_keys( $this->_actions );
            }
        $thing = $this->_stringify( $thing );
        if ( array_key_exists( $thing, $this->_interactions ) )
            {
                return $this->_interactions[ $thing ][ self::ACTIONS ];
            }
        return [];
    }

    public function getRequirements( $thing )
    {
        $class = $this->$this->reflectionHelper->getClass( $thing );
        if ( array_key_exists( $class, $this->_requirements ) )
            {
                return $this->_requirements[ $class ];
            }
        return array();
    }

    public function getDataobjects()
    {
        return $this->_dataobjects;
    }

}
