<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Reconciliation\Environment;

class Ignore extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter
{
    function getName()
    {
        return 'Delay';
    }

    function getKey()
    {
        return 'ignore_past_minutes';
    }

    function getNote()
    {
        return 'Do not process queue items sent in the past X minutes.';
    }

    function getDefault()
    {
        return 10;
    }
}
