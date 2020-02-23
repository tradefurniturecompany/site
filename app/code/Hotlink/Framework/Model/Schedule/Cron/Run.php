<?php
namespace Hotlink\Framework\Model\Schedule\Cron;

class Run extends \Hotlink\Framework\Model\Schedule\Cron\AbstractCron
{

    protected static $_reportSingleton = false;

    protected function _getReportContext()
    {
        return 'run';
    }

    protected function _initReport()
    {
        if ( self::$_reportSingleton )
            {
                self::$_reportSingleton->setSection( $this );
            }
        else
            {
                self::$_reportSingleton = $this->reportHelper->create( $this );
                $this->_initOnceReport();
            }
        return $this;
    }

    //
    //  Cron does not respect di configuration, so statics required to make a single report
    //
    function getReport( $safe = true )
    {
        if ( ! $this->_report && $safe )
            {
                if ( ! self::$_reportSingleton )
                    {
                        $this->_initReport();
                    }
                $this->setReport( self::$_reportSingleton );
            }
        return $this->_report;
    }

    //
    //  Invokes monitors
    //
    protected function _execute( $schedule )
    {
        $jobCode = $schedule->getJobCode();
        $monitor = $this->_extractMonitor( $jobCode );
        $report = $this->getReport();
        $report->addReference( $monitor->getInteraction()->getName() );
        $report->info( "monitor : " . $monitor->getName() );
        $report->info( "interaction : " . $monitor->getInteraction()->getName() );
        try
            {
                $report( $monitor , 'execute' );
                $report->incSuccess();
                $this->setReportStatus();
            }
        catch ( \Exception $e )
            {
                $report
                    ->incFail()
                    ->unindent()
                    ->fatal( $exception )
                    ->setStatus( \Hotlink\Framework\Model\Report::STATUS_EXCEPTION );
            }
    }

    protected function _extractMonitor( $jobCode )
    {
        $interaction = $this->factoryHelper->create( $this->_decodeJobInteraction( $jobCode ) );
        $monitor = $this->factoryHelper->create( $this->_decodeJobMonitor( $jobCode ), [ 'interaction' => $interaction ] );
        return $monitor;
    }

}