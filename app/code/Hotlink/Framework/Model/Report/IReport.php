<?php
namespace Hotlink\Framework\Model\Report;

interface IReport
{

    function getReport( $safe = true );
    function setReport( \Hotlink\Framework\Model\Report $report = null );
    function getReportSection();

}
