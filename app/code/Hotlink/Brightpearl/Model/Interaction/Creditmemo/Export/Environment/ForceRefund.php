<?php
namespace Hotlink\Brightpearl\Model\Interaction\Creditmemo\Export\Environment;

class ForceRefund extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Boolean
{
    public function getName()
    {
        return 'Force Refund';
    }

    public function getKey()
    {
        return 'force-refund';
    }

    public function getDefault()
    {
        return false;
    }

    public function getNote()
    {
        return 'Recreate a refund in Brightpearl even one has already been sent';
    }

}
