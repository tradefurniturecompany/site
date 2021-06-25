<?php
namespace Hotlink\Brightpearl\Model\Interaction\Creditmemo\Export\Environment;

class ForceQuarantine extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Boolean
{
    function getName()
    {
        return 'Force Quarantine';
    }

    function getKey()
    {
        return 'force-quarantine';
    }

    function getDefault()
    {
        return false;
    }

    function getNote()
    {
        return 'Recreate a quarantine note via the Api even if it has already been exported';
    }

}
