<?php
namespace Hotlink\Framework\Model\Interaction;

abstract class AbstractInteraction extends \Hotlink\Framework\Model\AbstractModel implements \Hotlink\Framework\Model\Report\IReport, \Hotlink\Framework\Html\IFormHelper
{

    protected $_implementation = false;
    protected $_config = false;
    protected $_environment = array();
    protected $_trigger;
    protected $_report;
    protected $_actionObjects = false;

    protected $conventionHelper;
    protected $configMap;
    protected $dateTimeDateTimeFactory;
    protected $storeManager;
    protected $htmlFormInteractionHelper;

    function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,

        \Hotlink\Framework\Helper\Convention\Interaction $conventionHelper,
        \Hotlink\Framework\Model\Config\Map $configMap,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateTimeDateTimeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Hotlink\Framework\Helper\Html\Form\Interaction $htmlFormInteractionHelper
    )
    {
        parent::__construct( $exceptionHelper, $reflectionHelper, $reportHelper, $factoryHelper );

        $this->conventionHelper = $conventionHelper;
        $this->configMap = $configMap;
        $this->dateTimeDateTimeFactory = $dateTimeDateTimeFactory;
        $this->storeManager = $storeManager;
        $this->htmlFormInteractionHelper = $htmlFormInteractionHelper;
    }

    abstract protected function _getName();

    function getName()
    {
        return ( string ) __( $this->_getName() );
    }

    function getKey()
    {
        return get_class($this);
    }

    //
    //  The convention is to infer a block class name from an interaction class name as follows:
    //    Given the interaction  :    \Module\Name\Some\Path\Interaction\Does\Something\Useful
    //    The block should be    :    \Module\Name\Adminhtml\Interaction\Admin\Tab\Does\Something\Useful
    //
    //  You can overload to provide explict block names if you wish to break from convention.
    //  Returning false indicates the interaction does not support an admin tab.
    //
    function getTabBlock()
    { // TODO: Rengage the convention
        return '\Hotlink\Framework\Block\Adminhtml\Interactions\Index\Tab\DefaultTab';
        return $this->conventionHelper->getTabBlock( $this );
    }

    function getImplementation()
    {
        if ( !$this->_implementation )
            {
                $implementation = false;
                $implementations = $this->configMap->getImplementations( $this );
                switch ( count( $implementations ) )
                    {
                        case 0:
                            $implementation = $this->conventionHelper->getImplementationClass( $this );
                            break;
                        case 1:
                            // Don't bother reading config - always use the defined class (so as to handle stale config)
                            $implementation = $implementations[ 0 ];
                            break;
                        default:
                            // Retrieve the one that has been configred for use
                            $implementation = $this->getEnvironment()->getImplementationModel();
                            if ( !$implementation )
                                {
                                    $implementation = $implementations[ 0 ];
                                }
                            break;
                    }
                if ( !$implementation )
                    {
                        $this->exception()->throwProcessing( 'No implementation found for interaction [class].', $this );
                    }
                $implementation = $this->factory()->create( $implementation );
                $implementation->setInteraction( $this );
                $this->_implementation = $implementation;
            }
        return $this->_implementation;
    }

    //
    //  Returns name of the class derived from \Hotlink\Framework\Model\Interaction\Environment\AbstractEnvironment
    //  You may need to overload this if the environment belongs to a different module.
    //
    function getEnvironmentClass()
    {
        return $this->conventionHelper->getEnvironmentClass( $this );
    }

    function setTrigger( \Hotlink\Framework\Model\Trigger\AbstractTrigger $trigger )
    {
        $this->_trigger = $trigger;
        return $this;
    }

    function getTrigger()
    {
        return $this->_trigger;
    }

    //
    //  IReport
    //
    function setReport( \Hotlink\Framework\Model\Report $report = null )
    {
        $this->_report = $report;
    }

    function getReport( $safe = true )
    {
        if ( !$this->_report && $safe )
            {
                $this->setReport( $this->report()->create( $this ) );
                $report = $this->getReport( false );
                $report
                    ->setUser( $this->getTrigger()->getUser()->getDescription() )
                    ->setTrigger( $this->getTrigger()->getName() )
                    ->setContext( $this->getTrigger()->getContextLabel() )
                    ->setEvent( $this->getTrigger()->getMagentoEvent()->getName() )
                    ->setProcess( $this->getName() );
                $report
                    ->addLogWriter()
                    ->addItemWriter()
                    ->addDataWriter();
            }
        return $this->_report;
    }

    function getReportSection()
    {
        return 'interaction';
    }

    function execute()
    {
        if ( $this->canExecute() )
            {
                $report = $this->getReport();
                $report->setStatus( \Hotlink\Framework\Model\Report::STATUS_PROCESSING );
                $report->debug('Date/Time: '.date("Y-m-d H:i:s", $this->dateTimeDateTimeFactory->create()->timestamp(time())));

                $this->beforeActions();

                $report->info( "Starting '" . $this->getName() . "'" )->indent();
                $writersFormat = $this->getWriterCodes( $report );
                $report->info("Using Report Writers: ".$writersFormat);
                try
                    {
                        $implementation = $this->getImplementation();
                        $report->info( "Using implementation '" . $implementation->getName() . "'" );
                        $report( $implementation, 'execute' );

                        $status = \Hotlink\Framework\Model\Report::STATUS_SUCCESS;
                        if (!$report->failed() && !$report->succeeded()) {
                           $status = \Hotlink\Framework\Model\Report::STATUS_NO_RESULT;
                        } elseif ($report->failed()) {
                            $status = \Hotlink\Framework\Model\Report::STATUS_ERRORS;
                        }
                        $report->setStatus($status);
                    }
                catch ( \Exception $exception )
                    {
                        $report->setStatus(\Hotlink\Framework\Model\Report::STATUS_EXCEPTION);
                        $report->fatal( $exception );
                        $report->incFail();
                    }
                $report->unindent()->info( "Ending '" . $this->getName() . "'" );
                $this->afterActions();
            }
        $this->shutdown();
        return $this;
    }

    function shutdown()
    {
        if ( $this->getReport( false ) )
            {
                $this->getReport()->close();
            }
    }

    protected function _getActionObjects()
    {
        if ( ! $this->_actionObjects )
            {
                $this->_actionObjects = [];
                foreach ( $this->getActions() as $actionClass )
                    {
                        $this->_actionObjects[ $actionClass ] = $this->factory()->create( $actionClass, [ 'interaction' => $this ] );
                    }
            }
        return $this->_actionObjects;
    }

    protected function executeActions( $method )
    {
        $report = $this->getReport();
        foreach ( $this->_getActionObjects() as $actionClass => $action )
            {
                try
                    {
                        $report( $action, $method, $this );
                    }
                catch ( \Exception $e )
                    {
                        $report->error( "$method action exception [$actionClass]", $e );
                    }
            }
    }

    protected function beforeActions()
    {
        $this->executeActions( 'before' );
    }

    protected function afterActions()
    {
        $this->executeActions( 'after' );
    }

    function getActions()
    {
        return $this->configMap->getActions( $this );
    }

    function canExecute( $throwException = false )
    {
        if ( $this->_canExecute( $throwException ) )
            {
                if ( $this->getEnvironment()->isEnabled() )
                    {
                        if ( $this->getEnvironment()->isTriggerEnabled( $this->getTrigger() ) )
                            {
                                return true;
                            }
                        else
                            {
                                if ( $throwException )
                                    {
                                        throw new \Hotlink\Framework\Model\Exception\Configuration( 'Trigger not enabled in environment' );
                                    }
                            }
                        return false;
                    }
                else
                    {
                        if ( $throwException )
                            {
                                throw new \Hotlink\Framework\Model\Exception\Configuration( 'Interaction not enabled in environment' );
                            }
                    }
            }
        return false;
    }

    //
    //  Overload to provide additional execution constraints in derived classes
    //
    protected function _canExecute( $throwException = false )
    {
        return true;
    }

    protected function _getCleanStoreId( $storeId )
    {
        if ( ! ( $this->storeManager->getStore( $storeId ) ) )
            {
                $this->exception()->throwProcessing( 'Invalid store ' . $storeId . ' on interaction [class].', $this );
            }
        return $this->storeManager->getStore( $storeId )->getStoreId();
    }

    function setEnvironment( \Hotlink\Framework\Model\Interaction\Environment\AbstractEnvironment $environment )
    {
        return $this->addEnvironment( $environment );
    }

    function addEnvironment( \Hotlink\Framework\Model\Interaction\Environment\AbstractEnvironment $environment )
    {
        $storeId = $environment->getStoreId();
        if ( isset( $this->_environment[ $storeId ] ) )
            {
                $this->exception()->throwProcessing( 'Environment has already been set for store ' . $storeId . ' on interaction [class].', $this );
            }
        $storeId = $this->_getCleanStoreId( $storeId );
        $this->_environment[ $storeId ] = $environment;
        return $this;
    }

    //
    //  Returns a copy of the registered environments - new environments can only be added via addEnvironment()
    //
    function getEnvironments()
    {
        $copy = array();
        foreach ( $this->_environment as $key => $value )
            {
                $copy[ $key ] = $value;
            }
        return $copy;
    }

    function hasEnvironment( $storeId = null )
    {
        return !empty( $this->_environment ) && !empty( $this->_environment[$storeId] );
    }

    function getEnvironment( $storeId = null )
    {
        if ( is_null( $storeId ) )
            {
                if ( count( $this->_environment ) > 0 )
                    {
                        reset( $this->_environment );
                        $storeId = key( $this->_environment );                 // Return the first (i.e. default) storeId.
                    }
                elseif ( $this->getTrigger() )
                    {
                        $storeId = $this->getTrigger()->getStoreId();          // Return the trigger's storeId.
                    }
            }
        return ( isset( $this->_environment[ $storeId ] ) ) ? $this->_environment[ $storeId ] : false;
    }

    function createEnvironment( $storeId )
    {
        $storeId = $this->_getCleanStoreId( $storeId );
        $args = [ 'interaction' => $this, 'storeId' => $storeId ];
        $environment = $this->factory()->create( $this->getEnvironmentClass(), $args );
        $this->addEnvironment( $environment );
        return $environment;
    }

    function newEnvironment( $storeId )
    {
        return $this->createEnvironment( $storeId );
    }

    // ask / the / one
    function getConfigClass()
    {
        return $this->conventionHelper->getConfigClass( $this );
    }

    function getConfig()
    {
        if ( ! $this->_config )
            {
                $this->_config = $this->factory()->create( $this->getConfigClass(), [ 'interaction' => $this ] );
            }
        return $this->_config;
    }

    //
    //  IFormHelper
    //
    function getFormHelper()
    {
        return $this->htmlFormInteractionHelper;
    }

    protected function getWriterCodes( $report )
    {
        $format = '';
        foreach ( $report->getWriters() as $writer )
            {
                $code = $writer->getCode();
                $format .= "$code ";
            }
        return $format;
    }

}
