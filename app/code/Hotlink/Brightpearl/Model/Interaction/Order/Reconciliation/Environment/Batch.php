<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Reconciliation\Environment;

class Batch extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter
{
    public function getName()
    {
        return 'Batch size';
    }

    public function getKey()
    {
        return 'batch';
    }

    public function getNote()
    {
        return 'Number of queue items in a batch';
    }

    public function getDefault()
    {
        return 100;
    }
}
