<?php
namespace Hotlink\Brightpearl\Model\Interaction\Creditmemo\Export\Environment;

class Force extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Boolean
{
    public function getName()
    {
        return 'Force';
    }

    public function getKey()
    {
        return 'force';
    }

    public function getDefault()
    {
        return false;
    }

    public function getNote()
    {
        return 'Resubmit creditmemo to Brightpearl API even if it has already been sent';
    }

}
