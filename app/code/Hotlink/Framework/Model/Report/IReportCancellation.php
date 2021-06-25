<?php
namespace Hotlink\Framework\Model\Report;

interface IReportCancellation
{

    function getStatus();
    function getStatusCancel();
    function setStatus( $value );

}
