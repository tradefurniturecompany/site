<?php
namespace Hotlink\Framework\Model\Schedule;

abstract class AbstractSchedule implements \Hotlink\Framework\Model\Report\IReport
{

    protected $_report;

    protected $config;
    protected $reportHelper;

    public function __construct(
        \Hotlink\Framework\Model\Schedule\Config $config,
        \Hotlink\Framework\Helper\Report $reportHelper
    )
    {
        $this->config = $config;
        $this->reportHelper = $reportHelper;
    }

    protected function getConfig()
    {
        return $this->config;
    }

    public function getReport( $safe = true )
    {
        if ( ! $this->_report && $safe )
            {
                $this->setReport( $this->reportHelper->create( $this ) );
            }
        return $this->_report;
    }

    public function setReport( \Hotlink\Framework\Model\Report $report = null )
    {
        $this->_report = $report;
        return $this;
    }

    public function getReportSection()
    {
        return 'schedule';
    }

}
