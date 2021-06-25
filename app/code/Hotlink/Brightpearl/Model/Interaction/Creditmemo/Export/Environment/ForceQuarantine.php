<?php
namespace Hotlink\Brightpearl\Model\Interaction\Creditmemo\Export\Environment;

class ForceQuarantine extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Boolean
{
    public function getName()
    {
        return 'Force Quarantine';
    }

    public function getKey()
    {
        return 'force-quarantine';
    }

    public function getDefault()
    {
        return false;
    }

    public function getNote()
    {
        return 'Recreate a quarantine note via the Api even if it has already been exported';
    }

}
