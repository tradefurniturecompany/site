<?php
namespace Hotlink\Framework\Model\Monitor;

abstract class AbstractMonitor implements \Hotlink\Framework\Model\Report\IReport
{

    protected $eventManager;
    protected $reportFactory;
    protected $conventionMonitorHelper;
    protected $configMap;
    protected $factoryHelper;
    protected $interaction;

    public function __construct(
        \Hotlink\Framework\Model\ReportFactory $reportFactory,
        \Hotlink\Framework\Helper\Convention\Monitor $conventionMonitorHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,
        \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction,
        \Hotlink\Framework\Model\Config\Map $configMap,
        \Magento\Framework\Event\ManagerInterface $eventManager
    )
    {
        $this->eventManager = $eventManager;
        $this->interaction = $interaction;
        $this->factoryHelper = $factoryHelper;
        $this->reportFactory = $reportFactory;
        $this->conventionMonitorHelper = $conventionMonitorHelper;
        $this->configMap = $configMap;
    }

    abstract public function execute();
    abstract public function getCronFieldName();
    abstract protected function _getName();

    protected $_config = false;
    protected $_report = false;

    public function getConfig()
    {
        if ( !$this->_config )
            {
                return $this->factoryHelper->create( $this->_getConfigClass(), [ 'interaction' => $this->interaction ] );
            }
        return $this->_config;
    }

    public function getName()
    {
        return __( $this->_getName() );
    }

    public function getInteraction()
    {
        return $this->interaction;
    }

    protected function _getTriggers()
    {
        return $this->_getMap()->getTriggers( $this );
    }

    protected function _getConfigClass()
    {
        return $this->conventionMonitorHelper->getConfig( $this );
    }

    protected function _getMap()
    {
        return $this->configMap;
    }

    public function trigger( $event, array $args = [] )
    {
        if ( !isset( $args[ 'interaction' ] ) )
            {
                $args[ 'interaction' ] = $this->getInteraction();
            }
        $this->getInteraction()->setReport( null );  // Must clear out old report if present
        $report = $this->getReport()->info( 'Executing interaction: ' . $this->getInteraction()->getName() );
        try
            {
                $this->eventManager->dispatch( $event, $args );
            }
        catch ( \Exception $e )
            {
                $report->error( $e )->incFail();
            }
    }

    //
    //  \Hotlink\Framework\Model\Report\IReport
    //
    public function getReport( $safe = true )
    {
        if ( !$this->_report && $safe )
            {
                $this->setReport($this->reportFactory->create());
            }
        return $this->_report;
    }

    public function setReport(\Hotlink\Framework\Model\Report $report = null)
    {
        $this->_report = $report;
        return $this;
    }

    public function getReportSection()
    {
        return 'monitor';
    }

}
