<?php
namespace Hotlink\Brightpearl\Model\Interaction\Creditmemo\Export\Environment;

class ForceSalesCredit extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Boolean
{
    function getName()
    {
        return 'Force Sales Credit';
    }

    function getKey()
    {
        return 'force-salescredit';
    }

    function getDefault()
    {
        return false;
    }

    function getNote()
    {
        return 'Recreate a sales credit via the Api even if it has already been exported';
    }

}
