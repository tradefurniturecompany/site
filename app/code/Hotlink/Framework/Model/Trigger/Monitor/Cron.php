<?php
namespace Hotlink\Framework\Model\Trigger\Monitor;

class Cron extends \Hotlink\Framework\Model\Trigger\AbstractTrigger
{

    function getMagentoEvents()
    {
        return [ 'hotlink_framework_monitor_cron' ];
    }

    function getContexts()
    {
        return [ 'on_clock' => 'On cron schedule' ];
    }

    function getContext()
    {
        return 'on_clock';
    }

    protected function _getName()
    {
        return 'Cron Scheduled Time';
    }

    protected function _execute()
    {
        $interaction = $this->getMagentoEvent()->getInteraction();
        $this->setInteractions( $interaction );
        return parent::_execute();
    }

}
