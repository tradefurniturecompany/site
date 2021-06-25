<?php
namespace Hotlink\Framework\Model\Schedule\Cron;

abstract class AbstractCron extends \Hotlink\Framework\Model\Schedule\AbstractSchedule
{

	const DELIM_CLASSES = '___';

	protected $request;
	protected $factoryHelper;
	protected $userFactory;
	protected $dateTimeFactory;
	protected $reflectionHelper;

	abstract protected function _initReport();
	abstract protected function _getReportContext();
	abstract protected function _execute( $thing );

	function __construct(
		\Magento\Framework\App\Console\Request $request,
		\Hotlink\Framework\Model\Schedule\Config $config,
		\Hotlink\Framework\Helper\Report $reportHelper,

		\Hotlink\Framework\Helper\Factory $factoryHelper,
		\Hotlink\Framework\Model\UserFactory $userFactory,
		\Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateTimeFactory,
		\Hotlink\Framework\Helper\Reflection $reflectionHelper
	)
	{
		$this->request = $request;
		$this->factoryHelper = $factoryHelper;
		$this->userFactory = $userFactory;
		$this->dateTimeFactory = $dateTimeFactory;
		$this->reflectionHelper = $reflectionHelper;

		parent::__construct( $config, $reportHelper );
	}

	function isRelevantRequest()
	{
		$group = $this->request->getParam( 'group' );
		return ( is_null( $group ) || ( $group == 'default' ) );
	}

	function execute( $thing )
	{
		if ( ! $this->isRelevantRequest() )
			{
				return $thing;
			}
		if ( $this->getConfig()->getLoggingEnabled() )
			{
				$this->_initReport();
			}
		$report = $this->getReport();
		$report
			->setBatch( $report->getBatch() + 1 )
			->indent()
			->trace( '@ before job execute' )
			->indent();
		$result = $this->_execute( $thing );
		$report
			->unindent()
			->trace( '@ after job execute' )
			->unindent();
		return $result;
	}

	protected function _initOnceReport()
	{
		$report = $this->getReport();
		$report
			// Required since the number of invocations of execute is unknown, so unknown when to close the report
			->setOnDestructReportFatalIfOpen( false )
			->setBatch( 0 )
			->setUser( $this->userFactory->create()->getDescription() )
			->setTrigger( 'Cron' )
			->setContext( $this->_getReportContext() )
			->addLogWriter()
			->addItemWriter()
			->addDataWriter();

		$report
			->debug( 'Date/Time: '. date( "Y-m-d H:i:s", $this->dateTimeFactory->create()->timestamp( time() ) ) )
			->trace( 'report initialised' );

		$report->getWriter( 'log' )->getLog()->setInteraction( 'Hotlink Cron Management' );
	}

	//
	//  Internals
	//
	protected function setReportStatus()
	{
		$report = $this->getReport();
		$status = \Hotlink\Framework\Model\Report::STATUS_SUCCESS;
		if ( !$report->failed() && !$report->succeeded() )
			{
				$status = \Hotlink\Framework\Model\Report::STATUS_NO_RESULT;
			}
		elseif ( $report->failed() )
			{
				$status = \Hotlink\Framework\Model\Report::STATUS_ERRORS;
			}
		$report->setStatus( $status );
		return $report;
	}

	protected function _encodeJob( \Hotlink\Framework\Model\Monitor\AbstractMonitor $monitor, \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction )
	{
		$monitorClass = $this->reflectionHelper->getClass( $monitor );
		$interactionClass = $this->reflectionHelper->getClass( $interaction );
		$jobCode = $monitorClass . self::DELIM_CLASSES . $interactionClass;
		return $jobCode;
	}

	protected function _decodeJobMonitor( $jobCode )
	{
		return $this->_decodeJobPart( $jobCode, 0 );
	}

	protected function _decodeJobInteraction( $jobCode )
	{
		return $this->_decodeJobPart( $jobCode, 1 );
	}

	protected function _decodeJobPart( $jobCode, $index )
	{
		$parts = explode( self::DELIM_CLASSES, $jobCode );
		return $parts[ $index ];
	}

}
