<?php
namespace Hotlink\Framework\Model\Monitor;

class Cron extends \Hotlink\Framework\Model\Monitor\AbstractMonitor
{

    function getCronFieldName()
    {
        return 'monitor_schedule_cron_expr';
    }

    protected function _getName()
    {
        return 'Cron Monitor';
    }

    function execute()
    {
        $this->trigger( 'hotlink_framework_monitor_cron' );
    }

}
