<?php
namespace Hotlink\Framework\Model\Schedule\Cron;

class Inject extends \Hotlink\Framework\Model\Schedule\Cron\AbstractCron
{

	protected $_reportInitialised = false;

	protected $configMap;

	function __construct(
		\Magento\Framework\App\Console\Request $request,
		\Hotlink\Framework\Model\Schedule\Config $config,
		\Hotlink\Framework\Helper\Report $reportHelper,
		\Hotlink\Framework\Helper\Factory $factoryHelper,
		\Hotlink\Framework\Model\UserFactory $userFactory,
		\Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateTimeFactory,
		\Hotlink\Framework\Helper\Reflection $reflectionHelper,

		\Hotlink\Framework\Model\Config\Map $configMap
	)
	{
		$this->configMap = $configMap;
		parent::__construct(
			$request,
			$config,
			$reportHelper,
			$factoryHelper,
			$userFactory,
			$dateTimeFactory,
			$reflectionHelper
		);
	}

	protected function _getReportContext()
	{
		return 'inject';
	}

	protected function _getRunClass()
	{
		return '\Hotlink\Framework\Model\Schedule\Cron\Run';
	}

	protected function _initReport()
	{
		if ( ! $this->_reportInitialised )
			{
				$this->_initOnceReport();
				$this->_reportInitialised = true;
			}
		return $this;
	}

	//
	//  Inject config before Magento cron mechanism executes
	//
	protected function _execute( $crontabConfigData )
	{
		/**
		 * 2020-02-23 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		 * `n98-magerun2 sys:cron:run <...>`:
		 * «Argument 1 passed to N98\Magento\Command\System\Cron\AbstractCronCommand::getSchedule()
		 * must be of the type array, boolean given»
		 */
		$report = $this->getReport();
		$report->indent();
		$report->info( 'Starting interaction cron config injection' );
		$interactions = $this->configMap->getInteractions();
		if ( empty( $interactions ) )
			{
				$report->trace( 'No Interactions found' );
			}
		else
			{
				$before = $report->getIndent();
				try
					{
						foreach ( $interactions as $interactionClass )
							{
								$interaction = $this->factoryHelper->create( $interactionClass );
								$interaction->createEnvironment( null );
								$report->indent();
								$monitors = $this->configMap->getMonitors( $interactionClass );
								$monitorCount = count ( $monitors );
								$report->trace( "Interaction " . $interaction->getName() . " has $monitorCount monitor(s)" );
								foreach ( $monitors as $monitorClass )
									{
										$report->indent();
										try
											{
												$monitor = $this->factoryHelper
														 ->create( $monitorClass, [ 'interaction' => $interaction ] );
												$jobCode = $this->_encodeJob( $monitor, $interaction );
												$cronExprPath = $this->_getCronExprConfigPath( $monitor, $interaction );
												$crontabConfigData[ 'default' ][ $jobCode ]
													= [ 'name'        => $jobCode,
														'instance'    => $this->_getRunClass(),
														'method'      => 'execute',
														'config_path' => $cronExprPath ];
												$report
													->info( "Injected cron for monitor " . $monitor->getName() )
													->incSuccess()
													->indent()
													->debug( "cronExprPath = $cronExprPath" )
													->unindent();
											}
										catch ( \Exception $e )
											{
												$report
													->error( "Unable to inject job for Monitor $monitorClass", $e )
													->incFail();
											}
										$report->unindent();
									}
								$report->unindent();
							}
						$this->setReportStatus();
					}
				catch ( \Exception $exception )
					{
						$report
							->setIndent( $before )
							->incFail()
							->fatal( $exception )
							->setStatus( \Hotlink\Framework\Model\Report::STATUS_EXCEPTION );
					}
			}
		$report->info( "Ending config injection" );
		$report->unindent();
		return $crontabConfigData;
	}

	protected function _getCronExprConfigPath( \Hotlink\Framework\Model\Monitor\AbstractMonitor $monitor, \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction )
	{
		$environment = $interaction->getEnvironment() ? : $interaction->createEnvironment( null );
		$path = $environment->getConfig()->getPath( $monitor->getCronFieldName() );
		return $path;
	}

}