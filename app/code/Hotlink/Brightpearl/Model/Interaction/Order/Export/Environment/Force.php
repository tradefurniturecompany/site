<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Export\Environment;

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
        return 'Resubmit order to Brightpearl API even if order has already been sent';
    }
}
