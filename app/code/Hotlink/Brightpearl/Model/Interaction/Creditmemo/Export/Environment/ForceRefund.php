<?php
namespace Hotlink\Brightpearl\Model\Interaction\Creditmemo\Export\Environment;

class ForceRefund extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Boolean
{
    function getName()
    {
        return 'Force Refund';
    }

    function getKey()
    {
        return 'force-refund';
    }

    function getDefault()
    {
        return false;
    }

    function getNote()
    {
        return 'Recreate a refund in Brightpearl even one has already been sent';
    }

}
