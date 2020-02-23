<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Reconciliation\Environment;

class Batch extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter
{
    function getName()
    {
        return 'Batch size';
    }

    function getKey()
    {
        return 'batch';
    }

    function getNote()
    {
        return 'Number of queue items in a batch';
    }

    function getDefault()
    {
        return 100;
    }
}
