<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Reconciliation\Environment;

class Startdate extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter
{

    function getName()
    {
        return 'Fallback start date';
    }

    function getKey()
    {
        return 'fallback_start_date';
    }

    function getNote()
    {
        return 'Rewrite processing start date. Formats accepted: <br> Y-m-d (i.e. 2017-01-11) or Y-m-d H:i:s (i.e. 2017-01-11 10:10:00). TimeZone is set to UTC.';
    }

    function getDefault()
    {
        return null;
    }
}
