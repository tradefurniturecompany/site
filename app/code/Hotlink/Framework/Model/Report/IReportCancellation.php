<?php
namespace Hotlink\Framework\Model\Report;

interface IReportCancellation
{

    public function getStatus();
    public function getStatusCancel();
    public function setStatus( $value );

}
