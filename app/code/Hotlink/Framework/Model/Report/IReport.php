<?php
namespace Hotlink\Framework\Model\Report;

interface IReport
{

    public function getReport( $safe = true );
    public function setReport( \Hotlink\Framework\Model\Report $report = null );
    public function getReportSection();

}
