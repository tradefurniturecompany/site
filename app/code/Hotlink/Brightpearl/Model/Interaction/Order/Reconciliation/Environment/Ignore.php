<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Reconciliation\Environment;

class Ignore extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter
{
    public function getName()
    {
        return 'Delay';
    }

    public function getKey()
    {
        return 'ignore_past_minutes';
    }

    public function getNote()
    {
        return 'Do not process queue items sent in the past X minutes.';
    }

    public function getDefault()
    {
        return 10;
    }
}
