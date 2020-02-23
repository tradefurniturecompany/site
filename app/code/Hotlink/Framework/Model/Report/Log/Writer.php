<?php
namespace Hotlink\Framework\Model\Report\Log;

class Writer extends \Hotlink\Framework\Model\Report\Writer\AbstractWriter
{

    const SAVE_INTERVAL_MS = 2.0;  // 2 seconds

    protected $_log = false;
    protected $_lastSave = 0;
    protected $_active = false;

    /**
     * @var \Hotlink\Framework\Model\Report\LogFactory
     */
    protected $interactionReportLogFactory;

    /**
     * @var \Hotlink\Framework\Helper\Exception
     */
    protected $interactionExceptionHelper;

    function __construct(
        \Hotlink\Framework\Model\Report\LogFactory $interactionReportLogFactory,
        \Hotlink\Framework\Helper\Exception $interactionExceptionHelper
    ) {
        $this->interactionReportLogFactory = $interactionReportLogFactory;
        $this->interactionExceptionHelper = $interactionExceptionHelper;
    }
    function getCode()
    {
        return 'log';
    }

    function getWriterId()
    {
        if ( $this->_log )
            {
                return $this->_log->getRecordId();
            }
        return parent::getWriterId();
    }

	/**            
	 * @used-by \Hotlink\Framework\Model\Report::getId()
	 * @used-by \Hotlink\Framework\Model\Schedule\Cron\AbstractCron::_initOnceReport() 
	 * @return bool
	 */
    function getLog()
    {
        if ( $this->isOpen() )
            {
                return $this->_log;
            }
        return false;
    }

    protected function setLogStats()
    {
        $report = $this->_getReportObject();
        $this->_log->setSuccess( $report->getSuccess() );
        $this->_log->setFail( $report->getFail() );
        $this->_log->setReport( $report );
        return $this;
    }

    protected function setLogStatus()
    {
        $this->_log->setStatus( $this->_getReportObject()->getStatus() );
        return $this;
    }

    protected function setLogStarted()
    {
        $this->_log->setStarted( $this->_getReportObject()->getTimeStarted() );
        return $this;
    }

    protected function setLogEnded()
    {
        $this->_log->setEnded( $this->_getReportObject()->getNow() );
        return $this;
    }

    protected function setLogReference()
    {
        $this->_log->setReference( $this->_getReportObject()->getReference() );
        return $this;
    }

    protected function saveLog()
    {
        $this->_log->save();
        $this->_lastSave = $this->_getReportObject()->getNow();
        return $this;
    }

    protected function _open( \Hotlink\Framework\Model\Report $report )
    {
        parent::_open( $report );

        $log = $this->interactionReportLogFactory->create();
        $log->setUser( $report->getUser() );                           // User
        $log->setTrigger( $report->getTrigger() );                     // Trigger
        $log->setContext( $report->getContext() );                     // Context
        $log->setEvent( $report->getEvent() );                         // Event
        $log->setInteraction( $report->getProcess() );                 // Process
        $this->_log = $log;

        $this->setLogStats()->setLogStatus()->setLogStarted()->setLogReference()->saveLog();

        $this->_active = true;
        return $this;
    }

    protected function _write( \Hotlink\Framework\Model\Report\Item $item )
    {
        $this->setLogStats()->setLogStatus();

        $now = $this->_getReportObject()->getNow();
        if ( ( $now - $this->_lastSave ) >= self::SAVE_INTERVAL_MS )
            {
                // Check record still exists (it may have been deleted by another user)
                if ( $this->_active && !$this->_log->exists() )
                    {
                        $this->interactionExceptionHelper->throwProcessing( 'Log record deleted', $this );
                    }
                //$this->_getReportObject()->trace( 'Saving log record' );   // annoying message, unnecessary re-entry
                $this->setLogReference()->saveLog();
            }
    }

    protected function _close()
    {
        $this->setLogStats()->setLogStatus()->setLogEnded()->setLogReference()->saveLog();
        $this->_active = false;
    }

}
