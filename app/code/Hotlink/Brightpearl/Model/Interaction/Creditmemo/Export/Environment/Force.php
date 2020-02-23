<?php
namespace Hotlink\Brightpearl\Model\Interaction\Creditmemo\Export\Environment;

class Force extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Boolean
{
    function getName()
    {
        return 'Force';
    }

    function getKey()
    {
        return 'force';
    }

    function getDefault()
    {
        return false;
    }

    function getNote()
    {
        return 'Resubmit creditmemo to Brightpearl API even if it has already been sent';
    }

}
